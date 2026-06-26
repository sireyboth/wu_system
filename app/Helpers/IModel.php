<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class IModel extends Model
{
    use HasFactory, SoftDeletes;

    // Auto combine when creating/updating
    public function setNameKhAttribute(string $value)
    {
        $this->attributes['name_kh'] = $value;
        $this->attributes['name']    = $this->combineName($value, $this->name_en ?? request('name_en'));
    }

    public function setNameEnAttribute(string $value)
    {
        $this->attributes['name_en'] = $value;
        $this->attributes['name']    = $this->combineName($this->name_kh ?? request('name_kh'), $value);
    }

    protected function combineName(string $name_kh, string $name_en): string
    {
        return "{$name_kh} ({$name_en})";
    }
}
