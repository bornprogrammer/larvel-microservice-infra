<?php

namespace Laravel\Infrastructure\ExcelExports;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Facades\ExcelDownloadServiceFacade;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Laravel\Infrastructure\Services\BaseService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

abstract class BaseExcelExportService extends BaseService implements WithColumnFormatting, WithHeadings, WithMapping
{
  use Exportable;
  protected string $fileNamePrefix;

  protected ?string $s3DirectoryName;

  protected string $defaultEmptyCellValue = "N/A";

  protected array $args;

  public function __construct(public Collection|array|null $data, ...$args)
  {
    $this->args = $args;
  }

  public function columnFormats(): array
  {
    return [
      'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ];
  }

  public abstract function withStaticHeadings(): array;

  public abstract function mapDataAgainstColumns(array|Model $item): array;

  public function headings(): array
  {
    return $this->withStaticHeadings();
  }

  public static function make($data): self
  {
    $excelExport = new static($data);
    return $excelExport;
  }

  public function saveToS3(): array|null
  {
    $fileName = $this->fileNamePrefix . date('Y-m-d') . '-' . time() . '.xlsx';
    $this->store($fileName, "public");
    return ExcelDownloadServiceFacade::generateExcelFile($fileName, $this->s3DirectoryName);
  }

  public function map($row): array
  {
    $mappedData = $this->mapDataAgainstColumns($row);
    $mappedData = $this->convertEmptyOrNull($mappedData);
    $transformedData = $this->transformRow($mappedData);
    return $transformedData;
  }

  protected function transformRow(array $row): array
  {
    return $row;
  }

  public function convertEmptyOrNull($row): array
  {
    foreach ($row as $key => $value) {
      if (empty($value) || is_null($value)) {
        $row[$key] = $this->defaultEmptyCellValue;
      }
    }
    return $row;
  }

  protected function setIfNotEmptyCellValue(string|int $value, callable $callback, string|int|float|null $defaultValueIfEmptyCellValue = null): string|int
  {
    $defaultValueIfEmptyCellValue = $defaultValueIfEmptyCellValue ?? $this->defaultEmptyCellValue;
    if ($value !== $this->defaultEmptyCellValue) {
      return $callback($value);
    }
    return $defaultValueIfEmptyCellValue;
  }
}
