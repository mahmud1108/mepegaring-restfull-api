<?php

namespace App\Http\Resources;

use App\Models\ScheduleDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemporaryScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $weather_code = [];
        $schedule_detail_date = [];
        $schedule_detail_hour = [];
        $schedule_detail_temperature = [];
        $schedule_detail_windspeed = [];
        $status = [];
        foreach ($this->schedule_detail as $schedule_detail) {
            $weather_code[] = $schedule_detail->weather_code;
            $schedule_detail_date[] = $schedule_detail->schedule_detail_date;
            $schedule_detail_hour[] = $schedule_detail->schedule_detail_hour;
            $schedule_detail_temperature[] = $schedule_detail->schedule_detail_temperature;
            $schedule_detail_windspeed[] = $schedule_detail->schedule_detail_windspeed;
            $status[] = $schedule_detail->status;
        }

        return [
            'schedule_id' => $this->schedule_id,
            'schedule_name' => $this->schedule_name,
            'name_place' => $this->name_place,
            'name_package' => $this->package->name_package,
            'temperature_package' => $this->package->temperature_package,
            'schedule_detail' => [
                'weather_code' => $weather_code,
                'schedule_detail_date' => $schedule_detail_date,
                'schedule_detail_hour' => $schedule_detail_hour,
                'schedule_detail_temperature' => $schedule_detail_temperature,
                'schedule_detail_windspeed' => $schedule_detail_windspeed,
                'status' => $status
            ]
        ];
    }
}
