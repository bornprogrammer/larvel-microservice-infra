<?php

namespace Laravel\Infrastructure\Mail;

use Laravel\Infrastructure\Exceptions\EmailEventListNotFoundException;
use Laravel\Infrastructure\Exceptions\SystemException;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Helpers\UtilHelper;

abstract class BaseSlugWithEventDetailsEmailService extends BaseSlugEmailService
{
    public function __construct(string $eventTypeForApproval)
    {
        parent::__construct($eventTypeForApproval);
        $this->eventList = [];
    }

    /**
     * will be used to build the multiple items  such as invoice, PO and many more
     *
     * @return void
     */
    protected function fetchAndProcessEventList(?array $params = []): void
    {
        $this->preFetchAndProcessEventList($params);
        $eventList = $this->fetchEventList($params);
        if (!ArrayHelper::isArrayValid($eventList)) {
            throw new EmailEventListNotFoundException();
        }
        $this->postFetchAndProcessEventList($eventList, $params);
    }

    protected function preFetchAndProcessEventList(?array $params = []): void
    {
    }

    protected function postFetchAndProcessEventList(?array $eventList, ?array $params = []): void
    {
        $this->eventList = $this->iterateAndProcessEventList($eventList, $params);
    }

    protected function iterateAndProcessEventList(?array $eventList, ?array $params = []): array
    {
        $builtEventList = [];
        if (ArrayHelper::isArrayValid($eventList)) {
            foreach ($eventList as $event) {
                $builtEventList[$event["id"]] = $this->transformEventItem($event, $params);
            }
        }
        return $builtEventList;
    }

    protected  function getCreatorUserOrgIdKeyNameFromEvent(): string
    {
        return "user_org_id";
    }

    protected function transformEventItem(array $item, ?array $params = []): array
    {
        return $item;
    }

    protected abstract function fetchEventList(?array $params = []): array|null;

    protected function getEventKeyNameFromRecipientsList(): string
    {
        return "id";
    }

    /**
     * expecting that recipient list will come from user org service
     *
     * @return string
     */
    protected function getUserOrgIdFromRecipients(array $recipient): string
    {
        return $recipient["user_org_id"];
    }

    // $html = view('users.edit', compact('user'))->render();

    protected function iterateRecipientsAndSendEmail(): void
    {
        $recipientWithEvent = []; //["user_id"=>"transformed_item"]  
        foreach ($this->recipients as $recipient) {
            $event = $this->extractOutEventFromEventListByEventIdFromRecipient($recipient);
            if ($this->isRecipientAllowedToReceiveAnEmail($recipient, $event)) {
                $userOrgId = $this->getUserOrgIdFromRecipients($recipient);
                if (!isset($recipientWithEvent[$userOrgId])) {
                    $recipientWithEvent[$userOrgId] = ["transformed_item" => $this->transformRecipientItem($recipient)];
                }

                $transformedItem = $recipientWithEvent[$userOrgId]["transformed_item"];
                $recipient = $this->transformEmailItem($transformedItem, $event);
                $this->send($recipient["email"], $recipient["mailable_data"]);
            }
        }
    }

    protected function extractOutEventFromEventListByEventIdFromRecipient(array $recipient): array
    {
        $eventIdOfRecipient = $recipient[$this->getEventKeyNameFromRecipientsList()];
        $event = $this->eventList[$eventIdOfRecipient];
        return $event;
    }

    protected function mergeEventListWithRecipients(callable $callable): array
    {
        if (!$callable) {
            UtilHelper::throwCatchAndReportException(new SystemException("Please provide a callback for merging events with recipients"));
        }
        $mergedData = [];
        foreach ($this->recipients as $recipient) {
            $event = $this->extractOutEventFromEventListByEventIdFromRecipient($recipient);
            $mergedData[] = call_user_func($callable, $recipient, $event);
        }
        return $mergedData;
    }

    protected function transformRecipientItem(array $recipient): array
    {
        return $recipient;
    }

    protected  function getCreatorCreatedByKeyNameFromEvent(): string
    {
        return "created_by";
    }
}
