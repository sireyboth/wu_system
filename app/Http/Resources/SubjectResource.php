<?php
namespace App\Http\Resources;

class SubjectResource extends IResource
{
    protected function toList(): array
    {
        return to_list($this, [
            'code'       => $this->code,
            'major'      => new MajorResource($this->whenLoaded('major')),
            'year_level' => $this->year_level,
            'semester'   => $this->semester,
            'credit'     => $this->credit,
        ]);
    }
}
