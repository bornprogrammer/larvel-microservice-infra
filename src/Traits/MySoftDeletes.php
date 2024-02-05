<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Infrastructure\Constants\EntityStatusConstant;

trait MySoftDeletes
{
  use SoftDeletes;

  protected function runSoftDelete()
  {
    $query = $this->setKeysForSaveQuery($this->newModelQuery());

    $time = $this->freshTimestamp();

    $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];

    $this->{$this->getDeletedAtColumn()} = $time;

    if ($this->usesTimestamps() && !is_null($this->getUpdatedAtColumn())) {
      $this->{$this->getUpdatedAtColumn()} = $time;

      $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
    }
    $columns['status'] = EntityStatusConstant::DELETED;

    $query->update($columns);

    $this->syncOriginalAttributes(array_keys($columns));

    $this->fireModelEvent('trashed', false);
  }
}
