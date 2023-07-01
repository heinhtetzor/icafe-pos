<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $orderData;
    public $orderMenusData;
    public $totalData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderMenusGrouped, $order, $total)
    {
        $this->orderData = $order;
        $this->orderMenusData = $orderMenusGrouped;
        $this->totalData = $total;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        $order_date_time = Carbon::parse($this->orderData->created_at)->format('h:i A d-M-Y');
        $subject = 'Order - '. $this->orderData->invoice_no;
        return $this->subject($subject)
        ->view('admin.orders.order_summary_email', [
            "orderMenus" => $this->orderMenusData,
            "order" => $this->orderData,
            "total" => $this->totalData,
            "datetime" => $order_date_time
        ]);
    }
}
