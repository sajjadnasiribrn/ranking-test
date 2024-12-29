<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'entry_fee' => $this->entry_fee,
            'manager' => new UserResource($this->manager),
            'participants' => UserResource::collection($this->participants),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
