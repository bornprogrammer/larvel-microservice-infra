<?php

namespace Laravel\Infrastructure\Console;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMailable extends Mailable
{

    use Queueable, SerializesModels;

    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->name);
        return $this->subject($this->subject)->markdown('contact.email');
        // return view('CashFlowdata.create');
    }
}
