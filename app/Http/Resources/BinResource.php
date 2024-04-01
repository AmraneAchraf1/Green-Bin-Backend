<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // unset bins from collector resource
        $collector = $this->collector;
        if($collector->bins ?? false){
            $collector->bins = [];
        }


        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'city' => $this->city,
            'is_active' => $this->is_active,
            'waste_type' => $this->waste_type,
            'image' => $this->image ? Storage::disk('public')->url('bins/' . $this->image) : null,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'collector' => new CollectorResource($collector),
            'trashes' => TrashResource::collection($this->trashes),
        ];
    }
}
