<?php

namespace Laravel\Infrastructure\SmartApproval;

use App\Constants\ApproverTypeConstant;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Models\BaseModel;
use Laravel\Infrastructure\SmartApproval\ApproverTypeConstant as SmartApprovalApproverTypeConstant;
use stdClass;

class SmartApprovalHelper
{
  public function isSmartApprovalActivityCreated(): bool
  {
    return true;
  }
}
