<?php
namespace App\Http\Resources;

class GuardianResource extends IResource
class GuardianResource extends IResource
{
    public function toList(): array
    {
        return to_list($this, [
            'job'          => $this->job,
            'job'          => $this->job,
            'relationship' => $this->relationship,
            'phones'       => $this->phones ?? null,
            'addresses'    => $this->addresses ?? null,
        ]);
    }
}
