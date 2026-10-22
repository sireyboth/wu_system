<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * name/email only — never log the password hash, even though it's
     * technically hashed already, it has no business sitting in an
     * otherwise-readable audit trail.
     */
    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    /**
     * Where a login should land after auth — a Lecturer has no reason to
     * see the full-school Dashboard (every batch/major/status, 1000+
     * students) on their way to their own two or three classes, so they
     * skip straight to their portal. Admin keeps the Dashboard even if
     * somehow also tagged Lecturer, same "Admin sees everything" rule as
     * every permission check elsewhere.
     */
    public function homeRouteName(): string
    {
        if ($this->hasRole('Lecturer') && ! $this->hasRole('Admin')) {
            return 'lecturer-portal.index';
        }

        return 'dashboard';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('User');
    }
}
