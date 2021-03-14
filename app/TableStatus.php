<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TableStatus extends Model
{
    protected $fillable = ['status', 'table_id', 'order_id'];

    public function isTableFree() {
        if($this->status === 0) return TRUE;

        return FALSE;
    }    

    
    public function table() {
        return $this->belongsTo('App\Table');
    }
    public function order() {
        return $this->belongsTo('App\Order');
    }

}
