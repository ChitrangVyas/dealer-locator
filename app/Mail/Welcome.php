<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Welcome extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $password;
    public $link;
    public $subject;
    protected $from_user;
    protected $from_user_name;
    public function __construct($user,$subject,$password,$link)
    {
        $this->user = $user;
        $this->password = $password;
        $this->link = $link;
        $this->subject = $subject;
        $this->from_user = config('mail.from.address');
        $this->from_user_name = config('mail.from.name');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        return $this->from($this->from_user,$this->from_user_name)->subject($this->subject)->view('emails.welcome',['user'=>$this->user,"password"=>$this->password,"link"=>$this->link]);
    }
}
