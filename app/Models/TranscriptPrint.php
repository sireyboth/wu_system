<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptPrint extends Model
{
    protected $fillable = ['doc_type', 'printed_by'];

    public function transcript()
    {
        return $this->belongsTo(Transcript::class);
    }

    public function printer()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }
}
