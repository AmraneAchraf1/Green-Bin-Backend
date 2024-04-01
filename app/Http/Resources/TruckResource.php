<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TruckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // remove truck from collector
        $collector = $this->collector;


        return [
            'id' => $this->id,
            'collector' => new CollectorResource($collector),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'working_days' => $this->working_days,
            'truck_capacity' => $this->truck_capacity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
