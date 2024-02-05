<?php

namespace Laravel\Infrastructure\Models;

use Laravel\Infrastructure\Models\BaseModel;

class PivotModel extends BaseModel
{
  public function newEloquentBuilder($query)
  {
    return new PivotModelEagerLoadBuilder($query);
  }
}
