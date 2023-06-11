<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    const EXPRESS = 99999;

    public function rules() {
        return [
            'name' => 'required|unique:tables,name,' . $this->id,
            'table_group_id' => 'required'
        ];
    }
    protected $fillable = ['name', 'table_group_id', 'status', 'is_processing', 'store_id'];
    
    public function table_status () {
        return $this->hasOne('App\TableStatus');
    }
    public static function getTablesAsc () {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        return Table::where('store_id', $store_id)
        ->orderBy('name')
        ->get();
    }

    public function setIsProcessing ($isProcessing) {
        $this->table_status->is_processing = $isProcessing;
        $this->table_status->save();
    }
    // public static function getTables() {
    //     return Table::with(['table_status' => function($query) {
    //         $query->where('status', 0);
    //     }])->get();
    // }    
}