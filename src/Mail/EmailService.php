<?php

namespace Laravel\Infrastructure\Mail;

use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Support\Facades\Blade;
use Laravel\Infrastructure\Console\ContactMailable;

use Laravel\Infrastructure\Facades\AwsS3BucketServiceFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Models\Contact;

use Illuminate\Support\Facades\Mail;
use Laravel\Infrastructure\Exceptions\InternalServerErrorException;
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

    protected MailableContract $mailable;

    public function __construct()
    {
        $this->init();
    }

    protected function init(): EmailService
    {
        $this->to = [];
        $this->cc = [];
        $this->bcc = [];
        $this->subject = "";
        return $this;
    }

    public function setToEmail(array|string $to): EmailService
    {
        $to = ArrayHelper::isArrayValid($to) ? $to : [$to];
        $this->to = $to;
        return $this;
    }

    public function setCCEmail(array $cc): EmailService
    {
        $this->cc = $cc;
        return $this;
    }

    public function setBCCEmail(array $bcc): EmailService
    {
        $orgnizationId = '';
        $orgId = RequestSessionFacade::getOrgId();
        if (isset($orgId) && !empty($orgId)) {
            $orgnizationId = $orgId;
        } else {
            $orgnizationId = RequestSessionFacade::getOrgIdFromQueryStrElseFromToken();
        }
        $this->bcc = $bcc;
        if ($orgnizationId == '0f9056c4-984a-4431-a3aa-db2cc147d597') {
            $this->bcc = ['klooqa@getkloo.com'];
        }
        return $this;
        //$this->bcc = $bcc;
        //return $this;
    }

    public function setSubjectEmail(string $subject): EmailService
    {
        $this->subject = $subject;
        return $this;
    }

    public function setMailable(MailableContract $mailable): EmailService
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

    public function getMailBodyContent(string $slug_name, array $content_data, array $to, array $bcc, array $cc, $orgId = null)
    {
        // $orgnizationId =  RequestSessionFacade::getOrgId();
        if (isset($orgId)) {
            $orgnizationId =  $orgId;
        } else {
            $orgnizationId = RequestSessionFacade::getOrgIdFromQueryStrElseFromToken();
        }
        $email_type = EmailType::where(['slug' => $slug_name])->first();

        if ($email_type->status == 'active') {

            $emailOrgNotification = EmailOrgNotification::where(
                [
                    'organization_id' => $orgnizationId,
                    'email_slug_id' => $email_type->id,
                    'is_enabled' => 'enabled'
                ]
            )->first();

            $emailOrgNotification->content = Blade::render(data_get($emailOrgNotification, "content"), $content_data);
            $emailOrgNotification->subject = Blade::render(data_get($emailOrgNotification, "subject"), $content_data);

            if (isset($emailOrgNotification['content']) && !empty($emailOrgNotification['content'])) {
                $emailBodyContent = $this->replaceEmailContentVariables($emailOrgNotification->content, $content_data);
                $emailSubject = $this->replaceEmailSubjectVariables($emailOrgNotification->subject, $content_data);

                $dataContent = [
                    "emailBodyContent" => $emailBodyContent,
                    "emailSubject" => $emailSubject
                ];

                return $dataContent;
            }
        }
        return false;
    }


    public function replaceEmailContentVariables($content, array $content_data)
    {
        if (isset($content_data['link'])) {
            $full_link = "<a href=" . $content_data['link'] . " target='_blank' style='text-decoration: none; color: #0052CC; display: inline-block; font-family: " . 'Poppins' . ",sans-serif;'>Click here</a>";
            $content =  $this->replaceContentVariables('{Click here}', $full_link, $content);
        }

        if (isset($content_data['clickThisLink'])) {
            $full_link = "<a href=" . $content_data['clickThisLink'] . " target='_blank' style='text-decoration: none; color: #0052CC; display: inline-block; font-family: " . 'Poppins' . ",sans-serif;'>Click this link</a>";
            $content =  $this->replaceContentVariables('{click_this_link}', $full_link, $content);
        }

        if (isset($content_data['linkSmallCase'])) {
            $full_link = "<a href=" . $content_data['linkSmallCase'] . " target='_blank' style='text-decoration: none; color: #0052CC; display: inline-block; font-family: " . 'Poppins' . ",sans-serif;'>click here</a>";
            $content =  $this->replaceContentVariables('{click here}', $full_link, $content);
        }

        if (isset($content_data['account_blance'])) {
            $acc_blance =  number_format(floatval(preg_replace('/[^\d.]/', '', $content_data['account_blance'])), 2);
            $content =  $this->replaceContentVariables('{account_blance}', $acc_blance, $content);
        }

        if (isset($content_data['requester_name'])) {
            $content =  $this->replaceContentVariables('{requester_name}', $content_data['requester_name'], $content);
        }

        if (isset($content_data['card_nickname'])) {
            $content =  $this->replaceContentVariables('{Card_nickname}', $content_data['card_nickname'], $content);
        }

        if (isset($content_data['issuer_name'])) {
            $content =  $this->replaceContentVariables('{issuer_name}', $content_data['issuer_name'], $content);
        }

        if (isset($content_data['vendor'])) {
            $content =  $this->replaceContentVariables('{vendor}', $content_data['vendor'], $content);
        }

        if (isset($content_data['amount'])) {
            $content =  $this->replaceContentVariables('{amount}', $content_data['amount'], $content);
        }

        if (isset($content_data['currency'])) {
            $content =  $this->replaceContentVariables('{currency}', $content_data['currency'], $content);
        }

        if (isset($content_data['card_name'])) {
            $content =  $this->replaceContentVariables('{card_name}', $content_data['card_name'], $content);
        }

        if (isset($content_data['card_number'])) {
            $content =  $this->replaceContentVariables('{card_number}', $content_data['card_number'], $content);
        }

        if (isset($content_data['card_type'])) {
            $content =  $this->replaceContentVariables('{card_type}', $content_data['card_type'], $content);
        }

        if (isset($content_data['decline_reason'])) {
            $content =  $this->replaceContentVariables('{decline_reason}', $content_data['decline_reason'], $content);
        }

        if (isset($content_data['account_name'])) {
            $content =  $this->replaceContentVariables('{account_name}', $content_data['account_name'], $content);
        }

        if (isset($content_data['account_number'])) {
            $content =  $this->replaceContentVariables('{account_number}', $content_data['account_number'], $content);
        }

        if (isset($content_data['sort_code'])) {
            $content =  $this->replaceContentVariables('{sort_code}', $content_data['sort_code'], $content);
        }

        if (isset($content_data['modulr_reason'])) {
            $content =  $this->replaceContentVariables('{modulr_reason}', $content_data['modulr_reason'], $content);
        }

        if (isset($content_data['institutionName'])) {
            $content =  $this->replaceContentVariables('{institution_name}', $content_data['institutionName'], $content);
        }

        if (isset($content_data['expiresAt'])) {
            $content =  $this->replaceContentVariables('{expires_at}', $content_data['expiresAt'], $content);
        }

        if (isset($content_data['invoice_number'])) {
            $content =  $this->replaceContentVariables('{invoice_number}', $content_data['invoice_number'], $content);
        }

        if (isset($content_data['invoice_date'])) {
            $content =  $this->replaceContentVariables('{invoice_date}', $content_data['invoice_date'], $content);
        }

        if (isset($content_data['invoice_due_date'])) {
            $content =  $this->replaceContentVariables('{invoice_due_date}', $content_data['invoice_due_date'], $content);
        }

        if (isset($content_data['view_btn_link'])) {
            $content =  $this->replaceContentVariables('{view_btn_link}', $content_data['view_btn_link'], $content);
        }

        if (isset($content_data['reject_btn_link'])) {
            $content =  $this->replaceContentVariables('{reject_btn_link}', $content_data['reject_btn_link'], $content);
        }

        if (isset($content_data['approve_btn_link'])) {
            $content =  $this->replaceContentVariables('{approve_btn_link}', $content_data['approve_btn_link'], $content);
        }

        if (isset($content_data['description'])) {
            $content =  $this->replaceContentVariables('{description}', $content_data['description'], $content);
        }

        if (isset($content_data['payee'])) {
            $content =  $this->replaceContentVariables('{payee}', $content_data['payee'], $content);
        }

        if (isset($content_data['submitters_name'])) {
            $content =  $this->replaceContentVariables('{submitters_name}', $content_data['submitters_name'], $content);
        }

        if (isset($content_data['organization_name'])) {
            $content =  $this->replaceContentVariables('{organization_name}', $content_data['organization_name'], $content);
        }

        if (isset($content_data['invoice_status'])) {
            $content =  $this->replaceContentVariables('{invoice_status}', $content_data['invoice_status'], $content);
        }

        if (isset($content_data['owner'])) {
            $content =  $this->replaceContentVariables('{owner}', $content_data['owner'], $content);
        }

        if (isset($content_data['delivery_date'])) {
            $content =  $this->replaceContentVariables('{delivery_date}', $content_data['delivery_date'], $content);
        }

        if (isset($content_data['requester_content'])) {
            $content =  $this->replaceContentVariables('{requester_content}', $content_data['requester_content'], $content);
        }
        if (isset($content_data['date'])) {
            $content =  $this->replaceContentVariables('{date}', $content_data['date'], $content);
        }
        if (isset($content_data['time'])) {
            $content =  $this->replaceContentVariables('{time}', $content_data['time'], $content);
        }

        if (isset($content_data['submit_btn_link'])) {
            $content =  $this->replaceContentVariables('{submit_btn_link}', $content_data['submit_btn_link'], $content);
        }

        if (isset($content_data['category_code'])) {
            $content =  $this->replaceContentVariables('{category_code}', $content_data['category_code'], $content);
        }

        if (isset($content_data['department'])) {
            $content =  $this->replaceContentVariables('{department}', $content_data['department'], $content);
        }

        if (isset($content_data['text_top'])) {
            $content =  $this->replaceContentVariables('{text_top}', $content_data['text_top'], $content);
        }

        if (isset($content_data['text_bottom'])) {
            $content =  $this->replaceContentVariables('{text_bottom}', $content_data['text_bottom'], $content);
        }
        if (isset($content_data['start_date'])) {
            $content =  $this->replaceContentVariables('{start_date}', $content_data['start_date'], $content);
        }
        if (isset($content_data['herelink'])) {
            $full_link = "<a href=" . $content_data['herelink'] . " target='_blank' style='text-decoration: none; color: #0052CC; display: inline-block; font-family: " . 'Poppins' . ",sans-serif;'>here</a>";
            $content =  $this->replaceContentVariables('{here}', $full_link, $content);
        }
        if (isset($content_data['entity_name'])) {
            $content =  $this->replaceContentVariables('{entity_name}', $content_data['entity_name'], $content);
        }
        if (isset($content_data['custom_form_fields'])) {
            $content =  $this->replaceContentVariables('{custom_form_fields}', $content_data['custom_form_fields'], $content);
        }
        if (isset($content_data['net_amount'])) {
            $content =  $this->replaceContentVariables('{net_amount}', $content_data['net_amount'], $content);
        }
        if (isset($content_data['tax_code'])) {
            $content =  $this->replaceContentVariables('{tax_code}', $content_data['tax_code'], $content);
        }
        if (isset($content_data['tax_rate'])) {
            $content =  $this->replaceContentVariables('{tax_rate}', $content_data['tax_rate'], $content);
        }
        return $content;
    }

    public function replaceEmailSubjectVariables($content, array $content_data)
    {
        if (isset($content_data['requester_name'])) {
            $content =  $this->replaceContentVariables('{requester_name}', $content_data['requester_name'], $content);
        }

        if (isset($content_data['invoice_status'])) {
            $content =  $this->replaceContentVariables('{invoice_status}', $content_data['invoice_status'], $content);
        }
        return $content;
    }

    public function replaceContentVariables($replace, $replace_to, $content)
    {
        if (strpos($content, $replace) !== false) {
            $content = str_replace($replace, $replace_to, $content);
        }
        return $content;
    }

    public function createEmailLog($userId, $action, $link, $emailData)
    {
        return (new EmailLogsRepository)->createEmailLog($userId, $action, $link, $emailData);
    }

    public function setAttachments(array $attachment, string $directory): EmailService
    {
        foreach ($attachment as $file) {
            $this->mailable->attachFromStorageDisk('s3', $directory . $file["fileName"]);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $attachment [["file_name"=>"file.jpg","link"=>"s3link"],["file_name"=>"file.jpg","link"=>"s3link"]] // link can be optional when file_name is full url
     * @return EmailService
     */
    public function setAttachmentsFromS3(?array $attachments, ?string $bucketDirectory = null, string $key = "filename"): EmailService
    {
        if (ArrayHelper::isArrayValid($attachments)) {
            foreach ($attachments as $attachment) {
                $fileName = $attachment[$key];
                $filePath = $bucketDirectory . '/' . $fileName;
                $this->mailable->attachFromStorageDisk('s3',  $filePath);
            }
        }
        return $this;
    }
}
