<?php

namespace Laravel\Infrastructure\Mail\ActionButtons;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Infrastructure\Constants\EntityStatusConstant;
use Laravel\Infrastructure\Models\EmailBasedAction;
use Laravel\Infrastructure\Models\EmailTypeAction;
use Illuminate\Encryption\Encrypter;
use Laravel\Infrastructure\Constants\ModuleConstants;
use Laravel\Infrastructure\Constants\EmailActionConstant;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Illuminate\Support\Str;

abstract class BaseEmailBasedActionButtonService implements EmailBasedActionButtonServiceInterface
{
    public function __construct(private readonly Encrypter $encrypter)
    {
    }

    public function getEmailTypeAction(string $slugId): Collection
    {
        $emailTypeActions = EmailTypeAction::join('email_action_buttons', 'email_type_actions.email_action_button_id', 'email_action_buttons.id')->where('email_type_actions.email_type_id', $slugId)->where('email_type_actions.status', EntityStatusConstant::ACTIVE)->orderby('email_action_buttons.order_btn_by', 'asc')->get();
        return $emailTypeActions;
    }

    public function createEmailBasedAction(array $params): array
    {
        $payload = $this->buildPayload($params);
        // dd($payload);
        EmailBasedAction::insert($payload);
        return $payload;
    }

    public function buildPayload(array $params): array
    {
        $insertPayload = [];
        foreach ($params as $param) {
            $event = $param["event"];
            $requestPayload = $param["request_payload"];
            $nextApprover = $param["approver"];
            $requestDetails = [
                'organization_id' => RequestSessionFacade::getOrgId(),
                'expired_on' => date('Y-m-d 23:59:59', strtotime("+14 days")),
                'user_details' => json_encode($event["event_creator"]["user"]),
                'request_data' => $requestPayload,
                'next_approver_mail_to' => [$nextApprover],
            ];
            $payload = [
                "module_belongs_to" => $event["email_content_data"]['module_constants'],
                "token" => $event["token"],
                "id" => Str::uuid()->toString(),
                "request_data" => json_encode($requestDetails),
                "is_actioned" => EmailActionConstant::PENDING,
                "request_id" => $event['id'],
                "is_actioned_for" => "email",
                "workflow_id" => $requestPayload['workflow_id'],
                "callback_url" => $event["email_content_data"]['call_back_url'],
                "created_at" => now(),
                "updated_at" => now(),
            ];
            $insertPayload[] = $payload;
        }
        return $insertPayload;
    }

    public function encryptButton(array $emailData): array
    {
        $encryptedEmailID = $this->encrypter->encrypt($emailData['email']);
        $encryptApproved = $this->encrypter->encrypt('approved');
        $encryptRejected = $this->encrypter->encrypt('rejected');
        $token = $emailData['token'];
        $landingPageURL = $emailData['landing_page_url'];
        $workflowType = $emailData["smart_approval_workflow_type"];
        $btnContentData = [
            'view_btn_link' => $emailData['platform_url'],
            'approve_btn_link' => env('APP_URL') . $landingPageURL . '?token=' . $token . '&is_actioned_by=' . $encryptedEmailID . '&status=' . $encryptApproved . "&type=" . $workflowType,
            'reject_btn_link' => env('APP_URL') . $landingPageURL . '?token=' . $token . '&is_actioned_by=' . $encryptedEmailID . '&status=' . $encryptRejected . "&type=" . $workflowType,
        ];
        return $btnContentData;
    }
}
