<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptSubject extends Model
{
    protected $fillable = [
        'subject_id', 'year', 'semester', 'position', 'code', 'title', 'credit', 'score', 'grade_point', 'grade',
    ];

    protected $casts = [
        'score'       => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function transcript()
    {
        return $this->belongsTo(Transcript::class);
    }

    /** No score yet = "IP" on the printout, and left out of the totals. */
    public function isInProgress(): bool
    {
        return $this->score === null;
    }
}
