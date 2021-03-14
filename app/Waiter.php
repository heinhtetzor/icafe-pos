<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Auth;

class Waiter extends Authenticatable
{
    use Notifiable;
    protected $fillable = ['name', 'username', 'password', 'status'];
    protected $hidden = ['password'];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
    public static function getCurrentWaiter() {
        return Auth()->guard('waiter')->user()->id;
    }

}
