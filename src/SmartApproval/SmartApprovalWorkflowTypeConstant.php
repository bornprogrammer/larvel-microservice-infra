<?php

namespace Laravel\Infrastructure\SmartApproval;

class SmartApprovalWorkflowTypeConstant
{
  public const SMART_APPROVAL_WORKFLOW_TYPE_CARD_APPROVAL = "card-approval";

  public const SMART_APPROVAL_WORKFLOW_TYPE_ACCOUNT_PAYABLE_APPROVAL = "account-payable-approval";

  public const SMART_APPROVAL_WORKFLOW_TYPE_ACCOUNT_PAYABLE_PAYNOW = "account-payable-paynow";

  public const SMART_APPROVAL_WORKFLOW_TYPE_CARD_EXPENSE_REVIEW = "card-expense-review";

  public const SMART_APPROVAL_WORKFLOW_TYPE_CARD_MONTHLY_AMOUNT_LIMIT = "card-monthly-amt";

  public const SMART_APPROVAL_WORKFLOW_TYPE_PURCHASE_ORDER_APPROVAL = "purchase-order-approval";

  public const SMART_APPROVAL_WORKFLOW_TYPE_PAYMENT_RUN = "account-payable-payment-runs";

  public const SMART_APPROVAL_WORKFLOW_TYPE_CREDIT_NOTE = "credit-note-approval";
}
