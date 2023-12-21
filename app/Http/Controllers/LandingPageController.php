<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Resources\TemporaryScheduleResource;
use App\Models\Package;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class LandingPageController extends Controller
{
    protected function forecast($latitude, $longitude, $start_date, $end_date)
    {
        $path = "https://api.open-meteo.com/v1/forecast?latitude=" . $latitude . "&longitude=" . $longitude . "&hourly=temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,weather_code&timezone=Asia%2FBangkok&start_date=" . $start_date . "&end_date=" . $end_date;

        try {
            $data = json_decode(file_get_contents($path), true);
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                'errors' => $th->getMessage()
            ]));
        }

        return $data;
    }

    public function post_forecast(Request $request)
    {
        $latitude = $request->input('latitude', '-8.3684');
        $longitude = $request->input('longitude', '114.3055');

        $start_date = $request->input('start_date', Carbon::now()->format('Y-m-d'));
        $end_date = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $data = $this->forecast($latitude, $longitude, $start_date, $end_date);

        return $data;
    }

    public function get_forecast()
    {
        $latitude =  '-8.3684';
        $longitude =  '114.3055';

        $start_date =  Carbon::now()->format('Y-m-d');
        $end_date =  Carbon::now()->format('Y-m-d');

        $data = $this->forecast($latitude, $longitude, $start_date, $end_date);

        return $data;
    }

    public function get_package_admin()
    {
        $package = Package::where('admin_id', 'like', "%adm_%")->get();

        return PackageResource::collection($package);
    }

    public function temporary_schedule(Request $request)
    {
        $package = Package::where('package_id', $request->package_id)->first();
        $add_date =  ceil($package->total_hour_package / 8);
        $latitude = $request->input('latitude', '-8.3684');
        $longitude = $request->input('longitude', '114.3055');
        $start_date = $request->input('start_date', Carbon::now()->format('Y-m-d'));
        $end_date = $request->input('end_date', date('Y-m-d', strtotime($start_date . ' +' . $add_date . ' days')));
        $place = $request->input('place', 'Wonosobo');
        $schedule_name = $request->input('schedule_name', 'Schedule Name');


        $schedule = new Schedule;
        $schedule->schedule_id = "sch_" . Random::generate();
        $schedule->name_place = $place;
        $schedule->schedule_name = $schedule_name;
        $schedule->schedule_date = $start_date;
        $schedule->package_id = $package->package_id;
        $schedule->save();

        $data = $this->forecast($latitude, $longitude, $start_date, $end_date);

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

        $weather_code_from_package = explode(',', $package->weather_package);
        $count_hour_package = $package->total_hour_package;
        $total_hour = 0;
        for ($i = 0; $i < count($dates); $i++) {
            $status = '';
            // untuk menentukan statusnya yes atau no
            //  yes berarti cuaca sesuai untuk mengeringkan komoditas dan no berarti sebaliknya

            // jika temperature dari API >= temperature dari package
            if ($temperatures[$i] >= $package->temperature_package) {
                // menentukan weather code
                for ($j = 0; $j < count($weather_code_from_package); $j++) {
                    // jika weather code dari API == weahter code dari package
                    if ($weather_codes[$i] == $weather_code_from_package[$j]) {
                        // jika total hour < dari jumlah total hour yang didapatkan dari package
                        if ($total_hour < $count_hour_package) {
                            $status = 'yes';
                            $total_hour++;
                        } else {
                            $status = '';
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
            $schedule_detail->schedule_id = $schedule->schedule_id;
            $schedule_detail->status = $status;
            $schedule_detail->schedule_id = $schedule->schedule_id;
            $schedule_detail->save();
        }
        return new TemporaryScheduleResource($schedule);

        return response()->json([
            "latitude" => $latitude,
            "longitude" => $longitude,
            "schedule_name" => $schedule_name,
            "place" => $place,
            "timezone" => "Asia/Bangkok",
            "timezone_abbreviation" => "+07",
            "hourly_units" => [
                "time" => "iso8601",
                "temperature_2m" => "°C",
                "relative_humidity_2m" => "%",
                "precipitation" => "mm",
                "wind_speed_10m" => "km/h"
            ],
            "hourly" => [
                'hour' => $hours,
                "date" => $dates,
                "temperature_2m" => $temperatures,
                "relative_humidity_2m" => $relative_humidities,
                "precipitation" => $precipitations,
                "wind_speed_10m" => $wind_speeds
            ]
        ]);
    }

    public function delete_temporary_schedule()
    {
        Schedule::where('user_id', null)->delete();

        return response()->json([
            'status' => true
        ]);
    }
}
