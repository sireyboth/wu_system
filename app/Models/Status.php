<?php
namespace App\Models;

use App\Helpers\IModel;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Status extends IModel
{
    public const STUDENT    = 'student';
    public const ENROLLMENT = 'enrollment';
    public const PAYMENT    = 'payment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = DEFAULT_FIELD_AND_SHORTCUT;

    public function scopeStudent(Builder $query)
    {
        return $this->get_by($query, self::STUDENT);
    }

    public function scopeEnrollment(Builder $query)
    {
        return $this->get_by($query, self::ENROLLMENT);
    }

    public function scopePayment(Builder $query)
    {
        return $this->get_by($query, self::PAYMENT);
    }

       public function snapshots()
    {
        return $this->hasMany(StudentSnapshot::class);
    }
}
