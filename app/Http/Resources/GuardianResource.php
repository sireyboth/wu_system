<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return to_list($this, [
            'occupation'   => $this->occupation,
            'relationship' => $this->pivot->relationship,
            'is_primary'   => $this->pivot->is_primary,
            'phones'       => $this->phones ?? null,
            'addresses'    => $this->addresses ?? null,
            // 'person'       => new PersonResource($this->whenLoaded('person')),
        ]);
    }
}

