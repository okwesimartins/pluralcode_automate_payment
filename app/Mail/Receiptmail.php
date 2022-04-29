<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Receiptmail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $title;
    public $user_info;
    public $trip_details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title,$user_info,$trip_details)
    {
        $this->title=$title;
        $this->user_info=$user_info;
        $this->trip_details=$trip_details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)->view('receipt');
    }
}
