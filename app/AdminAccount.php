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
        "username", "password", "store_id"
    ];
    protected $hidden = [
        "password"
    ];
    protected $appends = ['store_name'];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }


    public function getStoreNameAttribute() {
        return $this->store->name ?? "";
    }

    public function store () {
        return $this->belongsTo(Store::class);
    }
    
}
