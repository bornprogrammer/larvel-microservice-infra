<?php

namespace Laravel\Infrastructure\Mail;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Infrastructure\Constants\EntityStatusConstant;
use Laravel\Infrastructure\Exceptions\SlugEmailTypeNotFoundException;
use Laravel\Infrastructure\Facades\EmailServiceV2Facade;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Mail\CommonMailable;
use Laravel\Infrastructure\Models\EmailOrgNotification;
use Laravel\Infrastructure\Models\EmailType;
use Laravel\Infrastructure\Exceptions\EmailRecipientsNotFoundException;
use Laravel\Infrastructure\Exceptions\SlugEmailOrgNotificationNotFoundException;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;
use Laravel\Infrastructure\Helpers\EnvironmentHelper;
use Laravel\Infrastructure\Helpers\UtilHelper;

abstract class BaseSlugEmailService
{
    protected readonly string $slugName;
    protected ?EmailType $emailType;
    protected ?EmailOrgNotification $emailOrgNotification;
    protected string $emailTemplate;
    protected array|Collection|null $recipients;
    protected ?array $eventList;
    public function __construct(string $slugName)
    {
        $this->slugName = $slugName;
        $this->recipients = [];
    }
    public function handle(?array $params = []): void
    {
        try {
            $this->preHandle($params);
            $this->fetchAndProcessRecipients($params);
            $this->postHandle();
        } catch (\Throwable $th) {
            ExceptionReporterServiceFacade::report($th);
        }
    }

    protected function preHandle(?array $params = []): void
    {
        $this->getSlugDetails($params);
    }

    /**
     * fetching the slug details from DB
     *
     * @return void
     */
    protected function getSlugDetails(?array $params = []): void
    {
        $emailType = EmailType::where(['slug' => $this->slugName, "status" => EntityStatusConstant::ACTIVE])->first();
        if (!$emailType) {
            throw new SlugEmailTypeNotFoundException();
        }
        $emailOrgNotification = EmailOrgNotification::where(['organization_id' => $this->setOrgId(), 'email_slug_id' => $emailType->id, 'is_enabled' => 'enabled'])->first();
        if (!$emailOrgNotification) {
            throw new SlugEmailOrgNotificationNotFoundException();
        }
        $this->emailType = $emailType;
        $this->emailOrgNotification = $emailOrgNotification;
        $this->emailTemplate = $this->emailOrgNotification['content'];
    }

    protected function setOrgId(): string
    {
        return RequestSessionFacade::getOrgIdFromQueryStrElseFromToken();
    }

    protected function postHandle(): void
    {
    }

    protected function fetchAndProcessRecipients(?array $params = []): void
    {
        $this->preFetchAndProcessRecipients($params);
        $recipients = $this->fetchRecipients($params, $this->eventList);
        if (!ArrayHelper::isArrayValid($recipients)) {
            throw new EmailRecipientsNotFoundException;
        }
        $this->recipients = $recipients;
        $this->postFetchAndProcessRecipients();
    }

    protected function preFetchAndProcessRecipients(?array $params = []): void
    {
        $this->fetchAndProcessEventList($params);
    }

    protected function fetchAndProcessEventList(?array $params = []): void
    {
        $this->eventList = $params['events'] ?? null;
    }

    /**
     *
     * @param array|null $params
     * @param array|null $eventList
     * @return array|null
     */
    protected function fetchRecipients(?array $params = [], ?array $eventList = []): ?array
    {
        //TODO you can fetch the senders by extracting out user_org_ids from event list and call the orgms api;
        return [];
    }

    protected function postFetchAndProcessRecipients(): void
    {
        $this->iterateRecipientsAndSendEmailIfAllowed();
    }

    protected function iterateRecipientsAndSendEmailIfAllowed(): void
    {
        if ($this->isRecipientsIterable()) {
            $this->iterateRecipientsAndSendEmail();
        }
    }

    protected function iterateRecipientsAndSendEmail(): void
    {
        $this->recipients = $this->mergeRecipientsFromRequestReferer();
        foreach ($this->recipients as $recipient) {
            if ($this->isRecipientAllowedToReceiveAnEmail($recipient, $this->eventList)) {
                $recipient = $this->transformEmailItem($recipient, $this->eventList);
                $this->send($recipient["email"], $recipient["mailable_data"]);
            }
        }
    }

    protected function isRecipientAllowedToReceiveAnEmail(array $recipient, array $eventList): bool
    {
        return true;
    }

    protected function transformEmailItem(array $item, array $eventList): array
    {
        return $item;
    }

    protected function isRecipientsIterable(): bool
    {
        return $this->emailOrgNotification && $this->emailTemplate && (ArrayHelper::isArrayValid($this->recipients) || ($this->recipients instanceof Collection && $this->recipients->isNotEmpty())) && $this->isEmailToBeSentToAllSenders();
    }

    protected function send($recipientEmail, array $mailableData)
    {
        $attachments = $mailableData['attachments'] ?? null;
        $directory = $mailableData['attachmentDirectory'] ?? null;
        $attachmentStorageType = $mailableData['attachmentStorageType'] ?? null;

        $mailable = new CommonMailable($mailableData);
        EmailServiceV2Facade::setMailable($mailable)
            ->setToEmail($recipientEmail)
            ->setSubjectEmail($this->emailOrgNotification['subject'])
            ->setCCEmail($this->emailType["cc_email"])
            ->setBCCEmail($this->emailType["bcc_email"])
            ->setAttachments($attachmentStorageType, $attachments, $directory)
            ->sendEmail();
    }

    protected function isEmailToBeSentToAllSenders(): bool
    {
        $isEmailToBeSentToAllSenders = false;
        $kloQAOrgs = ["0f9056c4-984a-4431-a3aa-db2cc147d597"];
        if (EnvironmentHelper::isProduction() && in_array(RequestSessionFacade::getOrgIdFromQueryStrElseFromToken(), $kloQAOrgs) === false) {
            $isEmailToBeSentToAllSenders = true;
        } else {
            // $isEmailToBeSentToAllSenders = count($this->recipients) < 500;
            $isEmailToBeSentToAllSenders = true;
            //  || UtilHelper::isRequestValidViaReferer("invoice_approval_required", "true");
        }
        return $isEmailToBeSentToAllSenders;
    }

    protected function mergeRecipientsFromRequestReferer(): array
    {
        $emails = UtilHelper::getQueryStringValueViaViaReferer("invoice_approval_email");
        $emails = $emails ? explode(",", $emails) : [];
        $recipients = [];
        if (ArrayHelper::isArrayValid($emails)) {
            foreach ($this->recipients as $recipient) {
                if (in_array($recipient["approver_details"]["email"], $emails)) {
                    $recipients[] = $recipient;
                }
            }
            return $recipients;
        }
        return $this->recipients;
    }
}
