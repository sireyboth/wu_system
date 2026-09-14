<?php
namespace App\Http\Resources;

class RoomResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'code'     => $this->code,
            'capacity' => $this->capacity,
            'campus'   => new CampusResource($this->whenLoaded('campus')),
        ]);
    }
}
