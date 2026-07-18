<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'issue_date',
        'full_date_kh',
        'short_date_kh',
        'certificate_no',
        'status',
        'type',
        'remark',
    ];

    protected $casts = ['issue_date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Convenience scopes instead of separate models
    public function scopeProvisional(Builder $query)
    {
        return $query->where('type', 'provisional');
    }

    public function scopeStatus(Builder $query)
    {
        return $query->where('type', 'status');
    }
}
