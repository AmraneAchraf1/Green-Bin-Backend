<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trash extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bin_id',
        'trash_type',
        'image',
    ];

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
