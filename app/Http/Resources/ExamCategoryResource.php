<?php
namespace App\Http\Resources;

class ExamCategoryResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, []);
    }
}
