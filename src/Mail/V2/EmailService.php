<?php

namespace Laravel\Infrastructure\Mail\V2;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Blade;
use Laravel\Infrastructure\Console\ContactMailable;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Laravel\Infrastructure\Exceptions\InternalServerErrorException;
use Laravel\Infrastructure\Facades\AwsS3BucketServiceFacade;
use Laravel\Infrastructure\Models\EmailType;
use Laravel\Infrastructure\Facades\EmailServiceFacade;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;
use Laravel\Infrastructure\Models\EmailOrgNotification;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Repositories\EmailLogsRepository;
use Laravel\Infrastructure\Log\Logger;

class EmailService
{
    protected array $to;

    protected array $cc;

    protected array $bcc;

    protected string $subject;

    protected Mailable $mailable;

    public function __construct()
    {
        $this->init();
    }

    protected function init(): self
    {
        $this->to = [];
        $this->cc = [];
        $this->bcc = [];
        $this->subject = "";
        return $this;
    }

    public function setToEmail(array|string $to): self
    {
        $to = ArrayHelper::isArrayValid($to) ? $to : [$to];
        $this->to = $to;
        return $this;
    }

    public function setAttachments(string $storageDiskType, ?array $attachments, ?string $directory = "", string $key = "filename"): self
    {
        if (ArrayHelper::isArrayValid($attachments)) {
            foreach ($attachments as $attachment) {
                $fileName = $attachment[$key];
                if (!empty($directory)) {
                    $filePath = $directory . '/' . $fileName;
                } else {
                    $filePath = $fileName;
                }
                $this->mailable->attachFromStorageDisk($storageDiskType,  $filePath);
            }
        }
        return $this;
    }


    public function setCCEmail(array|null|string $cc): self
    {
        $ccEmails = [];
        if ($cc) {
            $ccEmails = ArrayHelper::isArrayValid($cc) ? $cc : explode(",", $cc);
        }
        $this->cc = $ccEmails;
        return $this;
    }

    public function setBCCEmail(array|null|string $bcc): self
    {
        // $bcc = $bcc && ArrayHelper::isArrayValid($bcc) ? $bcc : explode(",", $bcc);
        $bccEmails = [];
        if ($bcc) {
            $bccEmails = ArrayHelper::isArrayValid($bcc) ? $bcc : explode(",", $bcc);
        }
        $this->bcc = $bccEmails;
        return $this;
    }

    public function setSubjectEmail(string $subject): EmailService
    {
        $this->subject = $subject;
        return $this;
    }

    public function setMailable(Mailable $mailable): EmailService
    {
        $this->mailable = $mailable;
        return $this;
    }

    public function sendEmail(): void
    {
        try {
            $this->mailable->subject($this->subject);
            Mail::to($this->to)->cc($this->cc)->bcc($this->bcc)->send($this->mailable);
            $this->init();
        } catch (\Throwable $th) {
            ExceptionReporterServiceFacade::report($th);
        }
    }
}
