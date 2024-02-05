<?php

namespace Laravel\Infrastructure\ExcelExports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

abstract class ExcelExportFromCollectionService extends BaseExcelExportService implements FromCollection
{
  public function __construct(Collection $data, ...$args)
  {
    parent::__construct($data, $args);
  }

  public function collection()
  {
    return $this->data;
  }
}
