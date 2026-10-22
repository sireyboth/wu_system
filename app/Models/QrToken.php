<?php
namespace App\Models;

class QrToken extends IModel
{
    protected $fillable = ['class_session_id', 'token_hash', 'sequence', 'issued_at', 'expires_at', 'remark'];

    protected $casts = [
        'issued_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }
}
