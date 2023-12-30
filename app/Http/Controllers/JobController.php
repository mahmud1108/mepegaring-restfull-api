<?php

namespace App\Http\Controllers;

use App\Http\Resources\TemporaryScheduleResource;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\ScheduleNotification;
use App\Models\SettingBroadcast;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class JobController extends Controller
{
    public function send_notification()
    {
        $notifications = ScheduleNotification::orderBy('notification_date', 'asc')->get();
        foreach ($notifications as $notification) {
            if (Carbon::now()->format('Y-m-d') == $notification->notification_date) {
                $hour = $notification->notification_hour;
                $hour = Carbon::createFromFormat('H:i', $hour);
                $hour = $hour->subMinutes(30);
                $hour = $hour->format('H:i');
                if (Carbon::now()->format('H:i') == $hour) {
                    $tokens = SettingBroadcast::all();
                    if (count($tokens) > 0) {
                        foreach ($tokens as $token) {
                            try {
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => 'https://api.fonnte.com/send',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => array(
                                        'target' => $notification->user->phone,
                                        'message' => 'The weather is not condusive at ' . $notification->notification_hour . ' in ' . $notification->schedule->name_place
                                    ),
                                    CURLOPT_HTTPHEADER => array(
                                        'Authorization: ' . $token->token
                                    ),
                                ));

                                curl_exec($curl);
                                if (curl_errno($curl)) {
                                    $error_msg = curl_error($curl);
                                }
                                curl_close($curl);

                                if (isset($error_msg)) {
                                    return $error_msg;
                                }
                            } catch (\Throwable $th) {
                                throw new HttpResponseException(response([
                                    'errors' => $th->getMessage()
                                ]));
                            }
                        }
                    } else {
                        return response()->json([
                            'errors' => [
                                'Token not found'
                            ]
                        ]);
                    }
                }
            }
        }
        return response()->json([
            'user_phone' => $notification->user->phone,
            'status' => true
        ]);
    }

    public function schedule_update()
    {
        ScheduleDetail::truncate();
        ScheduleNotification::truncate();

        $schedules = Schedule::all();
        foreach ($schedules as $schedule) {
            $add_date =  ceil($schedule->package->total_hour_package / 8);
            $end_date = date('Y-m-d', strtotime($schedule->schedule_date . ' +' . $add_date . ' days'));

            $data = new LandingPageController;
            $data = $data->forecast($schedule->latitude, $schedule->longitude, $schedule->schedule_date, $end_date);

            $start_hour = 5;
            $end_hour = 16;

            $temperature = $data['hourly']['temperature_2m'];
            $relative_humidity = $data['hourly']['relative_humidity_2m'];
            $precipitation = $data['hourly']['precipitation'];
            $wind_speed = $data['hourly']['wind_speed_10m'];
            $times = $data['hourly']['time'];
            $weather_code = $data['hourly']['weather_code'];

            $temperatures = [];
            $relative_humidities = [];
            $precipitations = [];
            $wind_speeds = [];
            $dates = [];
            $hours = [];
            $weather_codes = [];

            // get temperature etc from 07.00 - 17.00
            foreach ($times as $index => $time) {
                $hour = (int) date('H', strtotime($time));
                if ($hour >= $start_hour && $hour <= $end_hour) {
                    $dates[] = date('Y-m-d', strtotime($time));
                    $hours[] = date('H:i', strtotime($time));
                    $temperatures[] = $temperature[$index];
                    $relative_humidities[] = $relative_humidity[$index];
                    $precipitations[] = $precipitation[$index];
                    $wind_speeds[] = $wind_speed[$index];
                    $weather_codes[] = $weather_code[$index];
                }
            }

            $weather_code_from_package = explode(',', $schedule->package->weather_package);
            $count_hour_package = $schedule->package->total_hour_package;
            $total_hour = 0;
            for ($i = 0; $i < count($dates); $i++) {
                $status = null;
                // untuk menentukan statusnya yes atau no
                //  yes berarti cuaca sesuai untuk mengeringkan komoditas dan no berarti sebaliknya

                // jika temperature dari API >= temperature dari package
                if ($temperatures[$i] >= $schedule->package->temperature_package) {
                    // menentukan weather code
                    for ($j = 0; $j < count($weather_code_from_package); $j++) {
                        // jika weather code dari API == weahter code dari package
                        if ($weather_codes[$i] == $weather_code_from_package[$j]) {
                            // jika total hour < dari jumlah total hour yang didapatkan dari package
                            if ($total_hour < $count_hour_package) {
                                $status = 'yes';
                                $total_hour++;
                            } else {
                                $status = null;
                            }
                        }
                    }
                }

                $schedule_detail = new ScheduleDetail;
                $schedule_detail->schedule_details_id =  'sch_details_' . Random::generate();
                $schedule_detail->weather_code = $weather_codes[$i];
                $schedule_detail->schedule_detail_date = $dates[$i];
                $schedule_detail->schedule_detail_hour = $hours[$i];
                $schedule_detail->schedule_detail_temperature = floor($temperatures[$i]);
                $schedule_detail->schedule_detail_windspeed = floor($wind_speeds[$i]);
                $schedule_detail->status = $status;
                $schedule_detail->schedule_id = $schedule->schedule_id;
                $schedule_detail->save();
            }

            $schedule_details = ScheduleDetail::where('status', null)->get();
            foreach ($schedule_details as $schedule_detail) {
                $notification = new ScheduleNotification;
                $notification->schedule_notification_id = 'notification_' . Random::generate();
                $notification->notification_date = $schedule_detail->schedule_detail_date;
                $notification->notification_hour = $schedule_detail->schedule_detail_hour;
                $notification->timezone = 'GMT +7';
                $notification->user_id = $schedule->user_id;
                $notification->schedule_id = $schedule->schedule_id;
                $notification->save();
            }

            return new TemporaryScheduleResource($schedule);
        }
    }
}
