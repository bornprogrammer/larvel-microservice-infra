<?php

namespace Laravel\Infrastructure\Models;

use Laravel\Infrastructure\Traits\ApplyAuditFilterTrait;

class SearchFilterConfig extends BaseModel
{
  // use ApplyAuditFilterTrait;

  protected $casts = ['is_searchable' => "boolean"];
}
