<?php

namespace Laravel\Infrastructure\DTOS;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginatedListWithFilterDTO extends BaseDTO
{
  public readonly LengthAwarePaginator $paginatedList;

  public readonly array $filterNodes;

  public static function fromParams(LengthAwarePaginator $paginatedList, array $filterNodes): self
  {
    return new self([
      "paginatedList" => $paginatedList,
      "filterNodes" => $filterNodes
    ]);
  }
}
