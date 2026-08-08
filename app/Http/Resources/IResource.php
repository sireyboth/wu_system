<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class IResource extends JsonResource
{
    protected bool $asParent = false;

    abstract protected function toList(): array;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->asParent ? parent::toArray($request) : $this->toList();
    }
}
