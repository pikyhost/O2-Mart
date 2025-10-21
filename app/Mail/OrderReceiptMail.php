<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('items.installationCenter', 'items.buyable');
    }

    public function build()
    {
        return $this->subject('Order Receipt #' . $this->order->id . ' – O2Mart')
                    ->view('emails.orders.receipt');
    }
}
