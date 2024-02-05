<?php

namespace Laravel\Infrastructure\Jobs\Traits;

use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Helpers\ArrayHelper;

/** 
 * UniqueJobTrait.php
 */
trait UniqueJobTrait
{
  public $uniqueFor = 86400;

  public function uniqueId()
  {
    $id = $this->extractOutIdFromData();
    return $id;
  }

  protected function extractOutIdFromData()
  {
    $idKey = $this->setUniqueIdKey();
    $eventData = $this->event?->data;
    if (ArrayHelper::isArrayValid($eventData)) {
      return $eventData[$idKey];
    } else if ($this->data instanceof Model) {
      return $eventData->{$idKey};
    } else {
      return "";
    }
  }

  protected function setUniqueIdKey(): string
  {
    return "id";
  }
}
