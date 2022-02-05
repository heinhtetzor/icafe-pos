<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    const EXPRESS = 0;

    public function rules() {
        return [
            'name' => 'required|unique:tables,name,' . $this->id,
            'table_group_id' => 'required'
        ];
    }
    protected $fillable = ['name', 'table_group_id', 'status', 'is_processing'];
    
    public function table_status () {
        return $this->hasOne('App\TableStatus');
    }
    public static function getTablesAsc () {
        return Table::orderBy('name')->get();
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