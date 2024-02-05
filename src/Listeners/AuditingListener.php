<?php

namespace Laravel\Infrastructure\Listeners;

use Laravel\Infrastructure\Facades\CustomAuditStateContainerFacade;
use Laravel\Infrastructure\Models\CustomAuditStateContainer;
use OwenIt\Auditing\Events\Auditing;

class AuditingListener
{
  public function __construct(private CustomAuditStateContainer $customAuditStateContainer)
  {
  }

  public function handle(Auditing $data)
  {
    $customAudit = config("audit.custom_audit");
    if (array_key_exists(get_class($data->model), $customAudit)) {
      // $data->model->transitionTo($data->model->audits()->first());
      $this->saveAuditState($data);
    }
  }

  protected function saveAuditState(Auditing $data): void
  {
    CustomAuditStateContainerFacade::setAuditState($data);
  }
}
