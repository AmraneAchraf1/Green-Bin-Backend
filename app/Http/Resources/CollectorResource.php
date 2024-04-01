<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CollectorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // chack if role contains ',' and split it
        $role = str_contains($this->role, ',') ? explode(',', $this->role) : array($this->role);

        //remove collector from bin resource
        $bins = $this->bins;
        foreach ($bins as $bin) {
            $bin->collector = null;
        }

        $trucks = $this->trucks;
        $trucks = $trucks->map(function ($truck) {
            $truck->collector = null;
            return $truck;
        });
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'role' => $role,
            'image' => Storage::disk('public')->url('images/' . $this->image),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bins' => BinResource::collection($bins),
            'trucks' => TruckResource::collection($trucks),
        ];
    }
}
