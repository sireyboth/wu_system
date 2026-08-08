<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Certificate extends IModel
{
    protected string $keyName = 'type';
    public const STATUS       = 'status';
    public const PROVISIONAL  = 'provisional';

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
        return $this->getBy($query, self::PROVISIONAL);
    }

    public function scopeStatus(Builder $query)
    {
        return $this->getBy($query, self::STATUS);
    }
}
