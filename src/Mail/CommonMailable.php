<?php

namespace Laravel\Infrastructure\Mail;

use Illuminate\Mail\Mailable;

class CommonMailable extends Mailable
{
    public array $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $result =  $this->view('klooviews::common-mailable')->with($this->data);
        return $result;
    }
}
