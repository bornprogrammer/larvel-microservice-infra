<?php

namespace Laravel\Infrastructure\Audits\Traits;

use Laravel\Infrastructure\Constants\LaravelAuditEventsConstant;

/**
 * 
 */
trait UpdateAuditTransformerTrait
{
  public array $newValues;
  public array $oldValues;
  public function transformAudit(array $data): array
  {
    if ($data['event'] === LaravelAuditEventsConstant::UPDATED) {
      $this->newValues = $this->transformNewValues($data['new_values']);
      $this->oldValues = $this->transformOldValues($data['old_values']);
      $data['new_values'] = $this->newValues;
      $data['old_values'] = $this->oldValues;
    }
    return $data;
  }

  public function transformNewValues(array $data): array
  {
    return $data;
  }

  public function transformOldValues(array $data): array
  {
    return $data;
  }
}
