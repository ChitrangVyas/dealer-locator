<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderEmailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;
    public $subject;
    public $from_user;
    public function __construct($order,$subject,$from_user)
    {
        //
        $this->order = $order;
        $this->subject = $subject;
        $this->from_user = $from_user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->from_user)->subject($this->subject)->view('emails.orders.quote',['order'=>$this->order]);
    }
}
