<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class IResource extends JsonResource
{
    protected bool $is_parent = false;
    abstract protected function toList(): array;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->is_parent ? parent::toArray($request) : $this->toList();
    }
}
