<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type'     => $this->type,
            'street'   => $this->street,
            'house_no' => $this->house_no,
            'province' => $this->province,
            'district' => $this->district,
            'commune'  => $this->commune,
            'village'  => $this->village,
        ];
    }
}
