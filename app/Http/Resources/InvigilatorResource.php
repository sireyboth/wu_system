<?php
namespace App\Http\Resources;

class InvigilatorResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'code'       => $this->code,
            'batch'       => $this->batch,
            'department'  => $this->department,
            'room'        => $this->room,
            'valid_until' => $this->valid_until?->format('Y-m-d'),
            'is_expired'  => $this->isExpired(),
            'public_url' => $this->public_url,
            'photo_url'  => $this->photo_url,
            'histories'  => $this->whenLoaded('histories', fn() => $this->histories->map(fn($h) => [
                'id'          => $h->id,
                'description' => $h->description,
                'date'        => $h->date?->format('Y-m-d'),
                'rating'      => $h->rating,
                'remark'      => $h->remark,
            ])),
        ]);
    }
}
