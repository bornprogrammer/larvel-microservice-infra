<?php

namespace Laravel\Infrastructure\ExcelExports;

use Maatwebsite\Excel\Concerns\FromArray;


abstract class ExcelExportFromArrayService extends BaseExcelExportService implements FromArray
{
  public function __construct(array|null $data = [])
  {
    parent::__construct($data);
  }

  public function array(): array
  {
    return $this->data;
  }
}
