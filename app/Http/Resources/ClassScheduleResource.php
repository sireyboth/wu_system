<?php
namespace App\Http\Resources;

class ClassScheduleResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'day_of_week' => $this->day_of_week,
            'starts_at'   => $this->starts_at,
            'ends_at'     => $this->ends_at,
            'class'       => new ClassSectionResource($this->whenLoaded('classSection')),
            'room'        => new RoomResource($this->whenLoaded('room')),
        ], false);
    }
}
