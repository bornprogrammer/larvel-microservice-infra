<?php

namespace Laravel\Infrastructure\Audits\Traits;

use Laravel\Infrastructure\Constants\LaravelAuditEventsConstant;

/**
 * 
 */
trait CreateAuditTransformerTrait
{
  public array $newValues;
  public array $oldValues;
  public function transformAudit(array $data): array
  {
    if ($data['event'] === LaravelAuditEventsConstant::CREATED) {
      $this->newValues = $this->transformNewValues($data['new_values']);
      $this->oldValues = [];
      $data['new_values'] = $this->newValues;
    }
    return $data;
  }

  public function transformNewValues(array $data): array
  {
    return $data;
  }
}
