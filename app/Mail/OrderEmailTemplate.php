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
    public $from_user_name;
    public function __construct($order,$subject,$from_user,$from_user_name='')
    {
        //
        $this->order = $order;
        $this->subject = $subject;
        $this->from_user = $from_user;
        /* if(!empty($from_user_name)){
            $this->mail_from_email = $from_user_name;
        }else{
            $this->from_user_name =  config('mail.from.name');
        } */
        $this->from_user_name = config('mail.from.name');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->from_user,$this->from_user_name)->subject($this->subject)->view('emails.orders.quote',['order'=>$this->order]);
    }
}
