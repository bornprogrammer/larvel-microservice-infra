<?php

namespace Laravel\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Support\Arr;

class CustomAudit extends BaseModel
{
  protected $table = "audits";

  public function transformAudit(array $data): array
  {
    $data = $this->addCustomValue($data);
    $data = $this->addPrimaryColumn($data);
    unset($data['new_values']['extra']);
    return $data;
  }

  protected function addCustomValue(array $data): array
  {
    $customValues = $data['new_values']['extra']['custom_values'];
    Arr::set($data, 'custom_values',  json_encode($customValues));
    return $data;
  }

  protected function addPrimaryColumn(array $data): array
  {
    $primaryId = $data['new_values']['extra']['primary_id'];
    if (is_string($primaryId) && strlen($primaryId) === 36) {
      Arr::set($data, 'auditable_id', $primaryId);
    }
    return $data;
  }

  public function getCreatedAtAttribute($value)
  {
    return (new Carbon($value))->setTimezone('Europe/London')->format('d/m/Y H:i');
  }


  public function getUpdatedAtAttribute($value)
  {
    return (new Carbon($value))->setTimezone('Europe/London')->format('d/m/Y H:i');
  }
}
