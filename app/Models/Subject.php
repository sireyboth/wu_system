<?php
namespace App\Models;

use App\Helpers\IModel;

class Subject extends IModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // DEFAULT_FIELD_AND_CODE may be defined as a constant array elsewhere.
        $base = [];
        if (defined('DEFAULT_FIELD_AND_CODE') && is_array(DEFAULT_FIELD_AND_CODE)) {
            $base = DEFAULT_FIELD_AND_CODE;
        }

        $this->fillable   = array_merge($base, ['year_level', 'major_id', 'semester', 'credit']);
        $this->searchable = array_merge($this->fillable, [
            'major.name', 'major.name_kh', 'major.name_en', 'major.shortcut',
        ]);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
