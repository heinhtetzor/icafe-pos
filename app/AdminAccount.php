<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class AdminAccount extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        "username", "password"
    ];
    protected $hidden = [
        "password"
    ];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
    
}
