<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;


    protected $fillable = [
        'collector_id',
        'latitude',
        'longitude',
        'is_active',
        'start_time',
        'end_time',
        'working_days',
        'truck_capacity',
    ];


    public function collector()
    {
        return $this->belongsTo(Collector::class);
    }
}
