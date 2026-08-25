<?php
namespace App\Http\Resources;

class UserResource extends IResource
{
    public function toList(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'verified'   => $this->email_verified_at,
            'roles'      => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
