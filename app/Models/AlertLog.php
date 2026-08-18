<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertLog extends Model
{
    protected $fillable = ['alert_id', 'type', 'message', 'sent_at', 'success'];

    protected $casts = [
        'sent_at' => 'datetime',
        'success' => 'boolean',
    ];

    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }
}
