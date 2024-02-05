<?php

namespace Laravel\Infrastructure\Listeners;

use Illuminate\Support\Facades\Event;
use Laravel\Infrastructure\Events\BaseCustomAuditEvent;
use Laravel\Infrastructure\Facades\CustomAuditStateContainerFacade;
use Laravel\Infrastructure\Models\CustomAudit;
use OwenIt\Auditing\Events\AuditCustom;

class CustomAuditListener
{
  public function __construct(private ?BaseCustomAuditEvent $customAuditEvent)
  {
  }

  public function handle(...$params)
  {
    $auditData = CustomAuditStateContainerFacade::getAuditState();
    $newValues = [];
    $oldValues = [];
    foreach ($auditData as $audit) {
      $auditEvent = $audit->model->auditEvent;
      $modelName = get_class($audit->model);
      $oldValues[$modelName][] = ["event" => $auditEvent, "data" => $audit->model->oldValues];
      $newValues[$modelName][] = ["event" => $auditEvent, "data" => $audit->model->newValues];
    }
    $newValues['extra']["custom_values"] = $this->transformCustomValues($oldValues, $newValues, $params);
    $newValues['extra']["primary_id"] = isset($params[0]) ? $params[0] : "";
    $this->executeAudit($oldValues, $newValues);
  }

  public function executeAudit(array $oldValues, array $newValues)
  {
    // $customAudit = CustomAudit::first();
    $customAudit = new CustomAudit();
    $customAudit->auditEvent = $this->customAuditEvent->auditEvent;
    $customAudit->isCustomEvent = true;
    $customAudit->auditCustomOld = $oldValues;
    $customAudit->auditCustomNew = $newValues;
    Event::dispatch(AuditCustom::class, [$customAudit]);
  }

  protected function transformCustomValues(array $oldValues, array $newValues, array $params): array
  {
    return [];
  }
}
