<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Order extends Model
{
    protected $fillable = ['status', 'table_id', 'waiter_id', 'invoice_no', 'total', 'paid_amount', 'customer_id', 'paid_amount'];

    protected $casts = [        
        "delete_logs" => 'array'
    ];

    const DRAFT = 0;
    const SUBMITTED = 1;

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
    public function waiter() {
        return $this->belongsTo('App\Waiter');
    }
    public function customer() {
        return $this->belongsTo('App\Customer');
    }
    public static function getExpressOrders ()
    {
        $express_orders = Order::where('table_id', Table::EXPRESS)
        ->orderby('created_at', 'DESC')
        ->simplePaginate(10);
        return $express_orders;
    }

    public static function generateInvoiceNumber() {
        //get today datevap
        $today = Carbon::today();
        $today_string = $today->format('Y-m-d');
        $latest_order = Order::whereDate('created_at', $today)->orderby('created_at', 'DESC')->first();
        if (!empty($latest_order)) {
            $arr = explode("_", $latest_order->invoice_no);
            $number = intval($arr[1]) + 1;
            $invoice_no = $today_string ."_". $number;
            return $invoice_no;
        }
        else {
            $invoice_no = $today_string ."_". 1;
            return $invoice_no;
        }        
    }

    public function isExpressOrder ()
    {
        if ($this->table_id === 0)
        {
            return true;
        }
        return false;
    }

    public function logDeletion ($data)
    {
        $new_data = $this->delete_logs ?? [];
        array_push($new_data, $data);        
        $this->delete_logs = $new_data;
        $this->save();
    }

}
