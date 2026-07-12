<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function hostedTrips()
    {
        return $this->hasMany(Trip::class, 'host_id');
    }

    public function tripMemberships()
    {
        return $this->hasMany(TripMember::class);
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_members')
            ->withPivot('role', 'joined_at');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
