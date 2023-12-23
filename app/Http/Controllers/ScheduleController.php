<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleStoreRequest;
use App\Http\Requests\ScheduleUpdateRequest;
use App\Http\Resources\ScheduleResource;
use App\Http\Resources\ScheduleRresource;
use App\Http\Resources\TemporaryScheduleResource;
use App\Models\Package;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use Carbon\Carbon;
use Dotenv\Repository\RepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Nette\Utils\Random;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $schedule = Schedule::query()->where('user_id', auth()->user()->user_id)
            ->paginate(perPage: $per_page, page: $page);

        return ScheduleResource::collection($schedule);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ScheduleStoreRequest $request)
    {
        $data = $request->validated();

        $package = Package::where('package_id', $data['package_id'])->first();
        $add_date =  ceil($package->total_hour_package / 8);
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        $start_date = $data['schedule_date'];
        $end_date = date('Y-m-d', strtotime($start_date . ' +' . $add_date . ' days'));
        $place = $data['name_place'];
        $schedule_name = $data['schedule_name'];


        $schedule = new Schedule;
        $schedule->schedule_id = "sch_" . Random::generate();
        $schedule->name_place = $place;
        $schedule->schedule_name = $schedule_name;
        $schedule->schedule_date = $start_date;
        $schedule->latitude = $latitude;
        $schedule->longitude = $longitude;
        $schedule->package_id = $package->package_id;
        $schedule->user_id = auth()->user()->user_id;
        $schedule->save();

        $data = new LandingPageController;
        $data = $data->forecast($latitude, $longitude, $start_date, $end_date);

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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::where('schedule_id', $id)->first();

        return new ScheduleResource($schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ScheduleUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        $schedule = Schedule::where('schedule_id', $id)->first();

        if (isset($data['latitude'])) {
            $schedule->latitude = $data['latitude'];
        }
        if (isset($data['longitude'])) {
            $schedule->longitude = $data['longitude'];
        }
        if (isset($data['name_place'])) {
            $schedule->name_place = $data['name_place'];
        }
        if (isset($data['schedule_name'])) {
            $schedule->schedule_name = $data['schedule_name'];
        }
        if (isset($data['schedule_date'])) {
            $schedule->schedule_date = $data['schedule_date'];
        }
        if (isset($data['package_id'])) {
            $schedule->package_id = $data['package_id'];
        }
        $schedule->save();

        return new ScheduleResource($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Schedule::where('schedule_id', $id)->delete();

        return response()->json([
            'status' => true
        ]);
    }
}
