<?php
namespace App\Http\Resources;

use function App\Helpers\to_list;
use function App\Helpers\to_name;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MajorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'name'     => to_name($this),
            'shortcut' => $this->shortcut,
            'faculty'  => new FacultyResource($this->faculty),
        ]);

        // return parent::toArray($request);
    }
}
