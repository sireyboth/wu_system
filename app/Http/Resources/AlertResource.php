<?php
namespace App\Http\Resources;

class AlertResource extends IResource
{
    public function toList(): array
    {
        return [
            'id'                       => $this->id,
            'title'                    => $this->title,
            'sub_title'                => $this->sub_title,
            'content'                  => $this->content,
            'category'                 => $this->category,
            'note'                     => $this->note,

            'start_date'               => $this->start_date?->format('Y-m-d\TH:i'),
            'end_date'                 => $this->end_date?->format('Y-m-d\TH:i'),

            'status'                   => $this->status,
            'completed_at'             => $this->completed_at?->format('Y-m-d H:i:s'),

            'repeat_type'              => $this->repeat_type,
            'repeat_interval'          => $this->repeat_interval,
            'repeat_until'             => $this->repeat_until?->format('Y-m-d'),

            'remind_enabled'           => (bool) $this->remind_enabled,
            'remind_interval_minutes'  => $this->remind_interval_minutes,

            'snoozed_until'            => $this->snoozed_until?->format('Y-m-d H:i:s'),
            'is_snoozed'               => $this->isSnoozed(),
            'is_overdue'               => $this->isOverdue(),
            'days_until_start'         => $this->daysUntilStart(),

            'remark'                   => $this->remark,
            'created_at'               => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'               => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at'               => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
