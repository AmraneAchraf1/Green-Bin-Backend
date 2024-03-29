<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Collector extends User
{
    use HasApiTokens, HasFactory, Notifiable;



    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'role',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }

    public function bins()
    {
        return $this->hasMany(Bin::class);
    }




}
