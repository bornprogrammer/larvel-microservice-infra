<?php

namespace Laravel\Infrastructure\Mail;

use App\MicroAppServices\OrganizationMicroAppService;
use Laravel\Infrastructure\Exceptions\EmailEventCreatorListNotFoundException;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\SmartApproval\SmartApprovalWorkflowTypeConstant;

trait EventCreatorTrait
{
    protected function fetchAndProcessEventCreatorList(?array $eventList, OrganizationMicroAppService $organizationMicroAppService, ?array $params = []): array|null
    {
        $eventCreatorList = [];
        if (ArrayHelper::isArrayValid($eventList)) {
            $creatorList = $this->fetchEventCreatorList($eventList, $organizationMicroAppService, $params);
            $eventCreatorList = $this->iterateAndProcessEventCreatorList($creatorList);
        }
        return $eventCreatorList;
    }

    protected function iterateAndProcessEventCreatorList(?array $creatorList): array
    {
        $newCreatorList = [];
        if (ArrayHelper::isArrayValid($creatorList)) {
            foreach ($creatorList as  $value) {
                $newCreatorList[$value['id']] = $value;
            }
        }
        return $newCreatorList;
    }

    protected function fetchEventCreatorList(?array $eventList, OrganizationMicroAppService $organizationMicroAppService, ?array $params = []): ?array
    {
        $eventCreatorIds = $this->extractOutEventCreatorIdsFromEventList($eventList, $params);
        $eventCreatorIds = array_unique($eventCreatorIds);
        $users = $organizationMicroAppService->getUserByUserOrgId($eventCreatorIds);
        if (!ArrayHelper::isArrayValid($users)) {
            throw new EmailEventCreatorListNotFoundException;
        }
        return $users;
    }

    /**
     * 
     *
     * @param array|null $eventList
     * @return array
     */
    protected function extractOutEventCreatorIdsFromEventList(?array $eventList, ?array $params = []): array
    {
        if (ArrayHelper::isArrayValid($eventList)) {
            if (isset($params['smart_approval_type']) && SmartApprovalWorkflowTypeConstant::SMART_APPROVAL_WORKFLOW_TYPE_PURCHASE_ORDER_APPROVAL == $params['smart_approval_type']) {
                $eventCreatorIds = array_column($eventList, "created_by");
            } else {
                $eventCreatorIds = array_column($eventList, "user_org_id");
            }

            return $eventCreatorIds;
        }
        return [];
    }

    // protected function appendEventCreatorDetailsToEventItem(array $item): array
    // {
    //     $item['creator_details'] = $this->eventCreatorList[$item[$this->getCreatorUserOrgIdKeyNameFromEvent()]];
    //     return $item;
    // }
}
