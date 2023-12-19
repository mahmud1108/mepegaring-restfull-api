<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'package_id' => $this->package_id,
            'name_package' => $this->name_package,
            'weather_package' => $this->weather_package,
            'temperature_package' => $this->temperature_package,
            'windspeed_package' => $this->windspeed_package,
            'total_hour_package' => $this->total_hour_package,
            'user_id' => $this->whenNotNull($this->user_id),
            'admin_id' => $this->whenNotNull($this->admin_id),
        ];
    }
}
