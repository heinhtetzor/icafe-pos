<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'business', 'phone', 'address', 'notes', 'status'];

    const INACTIVE = 0;
    const ACTIVE = 1;

    public function orders() {
        return $this->hasMany('App\Order');
    }
}
