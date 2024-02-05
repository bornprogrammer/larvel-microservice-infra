<?php

namespace Laravel\Infrastructure\DTOS;


use Laravel\Infrastructure\Constants\CustomExpenseFieldStatus;
use Laravel\Infrastructure\DTOS\BaseDTO;

class CreateUpdateCustomExpensesDTO extends BaseDTO
{
  public readonly string $transaction_id;

  public readonly ?array $customFormData;

  public readonly string $status;

  public readonly string $page_name;

  public static function fromParams(string $transactionId, ?array $customExpenseFields, string $pageName, string $status = CustomExpenseFieldStatus::CUSTOM_EXPENSE_FIELD_IN_PROCESS): self
  {
    return new self([
      'transaction_id' => $transactionId,
      'customFormData' => $customExpenseFields,
      'page_name' => $pageName,
      'status' => $status
    ]);
  }
}
