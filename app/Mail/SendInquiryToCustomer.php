<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInquiryToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $inquiry;
    public $subject;
    protected $from_user;
    protected $from_user_name;
    public function __construct($subject,$inquiry)
    {
        $this->inquiry = $inquiry;
        $this->subject = $subject;
        $this->from_user = config('mail.from.address');
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
        return $this->from($this->from_user,$this->from_user_name)->subject($this->subject)->view('emails.inquiry',['inquiry'=>$this->inquiry]);
    }
}
