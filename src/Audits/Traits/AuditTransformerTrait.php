<?php

namespace Laravel\Infrastructure\Audits\Traits;

/**
 * 
 */
trait AuditTransformerTrait
{
  public array $newValues;
  public array $oldValues;
  public function transformAudit(array $data): array
  {
    $this->newValues = $this->transformNewValues($data['new_values'], $data['event']);
    $this->oldValues = $this->transformOldValues($data['old_values'], $data['event']);
    $data['new_values'] = $this->newValues;
    $data['old_values'] = $this->oldValues;
    return $data;
  }

  public function transformNewValues(array $data, string $event): array
  {
    return $data;
  }

  public function transformOldValues(array $data, string $event): array
  {
    return $data;
  }
}
