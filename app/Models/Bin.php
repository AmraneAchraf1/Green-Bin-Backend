<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    use HasFactory;
    protected $fillable = [
        'collector_id',
        'latitude',
        'longitude',
        'address',
        'city',
        'is_active',
        'waste_type',
        'image',
        'description',
        'status',
    ];

    public function collector()
    {
        return $this->belongsTo(Collector::class);
    }

    public function trashes()
    {
        return $this->hasMany(Trash::class);
    }



}
