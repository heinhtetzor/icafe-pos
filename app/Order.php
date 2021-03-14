<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status', 'table_id', 'waiter_id'];

    public function getStatus() {
        //0 is unpaid
        //1 is paid
        return $this->status;
    }
    public function order_menus() {
        return $this->hasMany('App\OrderMenu');
    }
    public function table() {
        return $this->belongsTo('App\Table');
    }
}
