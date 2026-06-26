<?php
namespace App\Http\Resources;

use function App\Helpers\to_list;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'name'     => $this->name,
            'name_kh'  => $this->name_kh,
            'name_en'  => $this->name_en,
            'code'     => $this->code,
            'year'     => $this->year,
            'semester' => $this->semester,
            'credit'   => $this->credit,
        ]);
    }
}
