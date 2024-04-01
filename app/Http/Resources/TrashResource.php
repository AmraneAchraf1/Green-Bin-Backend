<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TrashResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // remove trash from bin
        $this->bin->trashes = [];
        // remove trash from user
        $this->user->trashes = [];

        // chack if trash_type contains ',' and split it
        $trash_type = str_contains($this->trash_type, ',') ? explode(',', $this->trash_type) : array($this->trash_type);

        return [
            'id' => $this->id,
            'trash_type' => $trash_type,
            'image' => $this->image ? Storage::disk('public')->url('trash/' . $this->image) : null,
            'bin' => new BinResource($this->bin),
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
