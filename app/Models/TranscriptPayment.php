<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptPayment extends Model
{
    protected $fillable = ['year', 'semester', 'verified_by', 'verified_at'];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function transcript()
    {
        return $this->belongsTo(Transcript::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
