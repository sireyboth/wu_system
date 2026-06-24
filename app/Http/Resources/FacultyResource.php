<?php
namespace App\Http\Resources;

use function App\Helpers\to_list;
use function App\Helpers\to_name;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
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
            'majors'   => $this->majors,
        ]);

        // return parent::toArray($request);
    }
}
