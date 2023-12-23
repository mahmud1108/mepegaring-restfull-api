<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'schedule_id' => $this->schedule_id,
            'name_place' => $this->name_place,
            'schedule_name' => $this->schedule_name,
            'schedule_date' => $this->schedule_date,
            'name_package' => $this->package->name_package,
            'weather_package' => $this->package->weather_package,
            'temperature_package' => $this->package->temperature_package,
            'relative_humidity_package' => $this->package->relative_humidity_package,
            'precipitation_package' => $this->package->precipitation_package,
            'windspeed_package' => $this->package->windspeed_package,
            'total_hour_package' => $this->package->total_hour_package,
        ];
    }
}
