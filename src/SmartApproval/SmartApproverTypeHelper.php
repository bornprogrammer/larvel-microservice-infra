<?php

namespace Laravel\Infrastructure\SmartApproval;

use App\Constants\ApproverTypeConstant;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Models\BaseModel;

class SmartApproverTypeHelper
{

  public static function isWorkflowStepApproverTypeAttribute(array|BaseModel|null $workflowStep): bool
  {
    return self::getWorkflowStepTypeAttribute($workflowStep) === SmartApprovalApproverTypeConstant::APPROVER_TYPE_ATTRIBUTE;
  }

  public static function isWorkflowStepApproverTypeUser(array|BaseModel|null $workflowStep): bool
  {
    return self::getWorkflowStepTypeAttribute($workflowStep) === SmartApprovalApproverTypeConstant::APPROVER_TYPE_USER;
  }

  public static function isWorkflowStepApproverTypeRole(array|BaseModel|null $workflowStep): bool
  {
    return self::getWorkflowStepTypeAttribute($workflowStep) === SmartApprovalApproverTypeConstant::APPROVER_TYPE_ROLE;
  }

  public static function getWorkflowStepTypeAttribute(array|BaseModel|null $workflowStep): ?string
  {
    if ($workflowStep) {
      $workflowStep = $workflowStep instanceof BaseModel ? ArrayHelper::convertObjectToArray($workflowStep) : $workflowStep;
      return $workflowStep['approver_type'];
    }
    return null;
  }
}
