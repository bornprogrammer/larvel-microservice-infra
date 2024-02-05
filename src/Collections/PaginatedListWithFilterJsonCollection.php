<?php

namespace Laravel\Infrastructure\Collections;

use Laravel\Infrastructure\DTOS\PaginatedListWithFilterDTO;
use Laravel\Infrastructure\Resources\BaseResourceCollection;

class PaginatedListWithFilterJsonCollection extends BaseResourceCollection
{
  public function __construct(protected PaginatedListWithFilterDTO $paginatedListWithFilterDTO)
  {
    parent::__construct($paginatedListWithFilterDTO->paginatedList);
  }

  public function toArray($request)
  {
    return [
      'data' => $this->collection,
      'meta' => $this->getMetas(),
    ];
  }

  protected function getMetas(): array
  {
    return [
      'filters' => $this->paginatedListWithFilterDTO->filterNodes
    ];
  }
}
