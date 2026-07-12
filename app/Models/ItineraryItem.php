<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'trip_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'estimated_budget',
        'order_index',
    ];

    protected $casts = [
        'estimated_budget' => 'decimal:2',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
