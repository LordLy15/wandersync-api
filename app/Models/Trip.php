<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'host_id',
        'name',
        'destination',
        'start_date',
        'end_date',
        'total_budget',
        'share_code',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_budget' => 'decimal:2',
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function members()
    {
        return $this->hasMany(TripMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'trip_members')
            ->withPivot('role', 'joined_at');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class)->orderBy('order_index');
    }
}
