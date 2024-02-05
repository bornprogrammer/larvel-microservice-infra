<?php

namespace Laravel\Infrastructure\Helpers;

use Laravel\Infrastructure\Console\ContactMailable;

use Laravel\Infrastructure\Models\Contact;

use Mail;

class EmailHelper
{
    protected array $cc;

    protected array $bcc;

    protected string $subject;

    public function __construct()
    {
        $this->init();
    }

    public static function getEmail(): EmailHelper
    {
        $email = new EmailHelper();
        return $email;
    }

    protected function init(): EmailHelper
    {
        $this->cc = [];
        $this->bcc = [];
        $this->subject = "";
        return $this;
    }

    public function ccEmail(array $cc): EmailHelper
    {
        $this->cc = $cc;
        return $this;
    }

    public function bccEmail(array $bcc): EmailHelper
    {
        $this->bcc = $bcc;
        return $this;
    }

    public function subjectEmail(string $subject): EmailHelper
    {
        $this->subject = $subject;
        return $this;
    }


    // public function sendEmail($val)
    public function sendEmail()
    {
        // return $val;
        // Mail::to(config('contact.send_email_to'))->send(new ContactMailable($request->name));
        // Mail::to('Ankit.Rathore@blenheimchalcot.com')->send(new ContactMailable($val));
        Mail::to('Ankit.Rathore@blenheimchalcot.com')
            ->cc([''])
            ->bcc([''])
            ->subject('Test Mail')
            ->send(new ContactMailable($val));
        // Contact::create($request->all());
        $contact = new Contact();
        $contact->name = $val;
        $contact->save();

        return 'success';
    }
}
