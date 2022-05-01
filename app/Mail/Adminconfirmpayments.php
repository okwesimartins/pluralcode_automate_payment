<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Adminconfirmpayments extends Mailable
{
    use Queueable, SerializesModels;

    public $admin_title;
    public $student_info;
    public $transaction_details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admin_title,$student_info,$transaction_details)
    {
        $this->admin_title=$admin_title;
        $this->student_info=$student_info;
        $this->transaction_details=$transaction_details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)->view('confirmation');
    }
}
