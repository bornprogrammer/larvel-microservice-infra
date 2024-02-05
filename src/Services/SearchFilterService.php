<?php

namespace Laravel\Infrastructure\Services;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Laravel\Infrastructure\Constants\SearchFilterDataTypeConstant;
use Laravel\Infrastructure\Constants\SearchFilterOperatorTypeConstant;
use Laravel\Infrastructure\Helpers\DateHelper;

class SearchFilterService extends BaseService
{

  private string $separator = "|";
  public function explodeFilters(string $filterString): array
  {
    $explodeArray = explode("^", $filterString);

    foreach ($explodeArray as $filter) {
      $splitArray = preg_split("/(\:|~)/", $filter);
      $value  = $splitArray[2];
      $column = $splitArray[1];
      $filters[] = [
        "rule" => $splitArray[0],
        "column" => $column,
        "value" => $value,
      ];
    }
    return $filters;
  }

  protected function buildColumnName(string $fieldName): string|Expression
  {
    if ($fieldName) {
      $fieldNames = explode(",", $fieldName);
      if (count($fieldNames) > 1) {
        $concatString = "CONCAT(" . $fieldNames[0];
        for ($i = 1; $i < count($fieldNames); $i++) {
          $concatString .= "," . "' '," . $fieldNames[$i];
        }
        $concatString .= ")";
        return DB::raw($concatString);
      }
    }
    return $fieldName;
  }

  protected function buildQueryForEachField($query, $operator, $column, $value, $nullCheckValue = null)
  {
    switch ($operator) {
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_EQUAL:
        $query->where($column, $value);
        break;
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_LIKE:
        $query->where($column, 'like', '%' . $value . '%');
        break;
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_WHEREIN:
        // $query->whereIn($column, $value);
        if (in_array($nullCheckValue, $value)) {
          $query->where(function ($query) use ($column, $value) {
            $query->whereIn($column, $value)->orWhereNull($column);
          });
        } else {
          $query->whereIn($column, $value);
        }
        break;
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_BETWEEN:
        $query->where($column, ">=", $value[0] ?? "")->where($column, "<=", $value[1] ?? "");
        break;
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_GREATER_THAN:
        $query->where($column, '>', $value);
        break;
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_GREATER_THAN_EQUALTO:
        $query->where($column, '>=', $value);
        break;
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_LESS_THAN:
        $query->where($column, '<', $value);
        break;
      case SearchFilterOperatorTypeConstant::OPERATOR_TYPE_LESS_THAN_EQUAL_TO:
        $query->where($column, '<=', $value);
        break;
      default:
        # code...
        break;
    }
  }

  public function buildQueryForFilter($query, $operator, $column, $value, array $fieldDetails, callable|null $queryCallback = null)
  {
    if (isset($fieldDetails[$column])) {
      $fieldDetail = $fieldDetails[$column];
      $column = !empty($fieldDetail['join_field_name']) ? $fieldDetail['join_field_name'] : $fieldDetail['field_name'];
      $column = $this->buildColumnName($column);
      $value =  $this->convertToSpecificType($value, $fieldDetail['data_type']);
      $nullCheckValue = !empty($fieldDetail['null_check_value']) ? $fieldDetail['null_check_value'] : null;
      $this->buildQueryForEachField($query, $operator, $column, $value, $nullCheckValue);
      $queryCallback ? $queryCallback($query, $column, $value, $fieldDetail) : "";
    }
  }

  // // 15/12/1990 >= && <= 15/12/1990

  public function buildQueryForSearch($query, $operator, $column, $value, array $fieldDetails, callable|null $queryCallback = null)
  {
    $fieldDetail = $fieldDetails[$column];
    $column = !empty($fieldDetail['search_column']) ? $fieldDetail['search_column'] : (!empty($fieldDetail['join_field_name']) ? $fieldDetail['join_field_name'] : "field_name");
    $column = $this->buildColumnName($column);
    $this->buildQueryForEachField($query, $operator, $column, $value);
    $queryCallback ? $queryCallback($query, $column, $value, $fieldDetail) : "";
  }

  protected function convertToSpecificType($value, $dataType): string|array
  {
    switch ($dataType) {
      case SearchFilterDataTypeConstant::SEARCH_FILTER_TYPE_DATE:
      case SearchFilterDataTypeConstant::SEARCH_FILTER_TYPE_DATETIME:
        $value = $this->convertDateToDateRange($value);
        break;
      case SearchFilterDataTypeConstant::SEARCH_FILTER_TYPE_ARRAY:
        $value = explode($this->separator, $value);
        break;
      default:
        break;
    }
    return $value;
  }

  protected function convertDateToDateRange(string $value): array
  {
    $dateRange = array_map(function ($dateString) {
      return DateHelper::fromDateString($dateString);
    }, explode($this->separator, $value));
    $dateRange[0] = $dateRange[0] . " 00:00:00" ?? "";
    $dateRange[1] = $dateRange[1] . " 23:59:59" ?? "";
    return $dateRange;
  }
}
