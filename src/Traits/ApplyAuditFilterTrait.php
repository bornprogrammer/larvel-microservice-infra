<?php

namespace Laravel\Infrastructure\Traits;

use Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait ApplyAuditFilterTrait
{
    private function explodeFilters($filterString): array
    {
        $explodeArray = explode("^", $filterString);

        foreach ($explodeArray as $filter) {
            $splitArray = preg_split("/(\:|~)/", $filter);

            $rule = $splitArray[0];
            $value  = $splitArray[2];

            $column = str_contains($splitArray[1], "->") ? join(".", explode("->", $splitArray[1])) : $splitArray[1];

            if ($rule === "whereIn" || $rule === "between") {
                $value = explode("|", $splitArray[2]);

                if ($column === "codat_push_operation_details.status") {
                    foreach ($value as $val) {
                        $value[] = str_replace(' ', '_', $val);
                    }
                }
            }

            $filters[] = [
                "rule" => $splitArray[0],
                "column" => $column,
                "value" => $value,
            ];
        }
        return $filters;
    }

    private function switchFilters($query, $rule, $column, $value): ?Builder
    {
        switch ($rule) {
            case 'equal':
                if ($column === "created_date") {
                    $column = 'audits.created_at';
                    $value = array_map(function ($element) {
                        return Carbon::parse(str_replace('/', '-', $element))->format('Y-m-d');
                    }, $value);
                    str_contains($column, "created_at") ? $query->whereDate($column, $value) : $query->where($column, $value);
                } else {
                    str_contains($column, "created_at") ? $query->whereDate($column, $value) : $query->where($column, $value);
                }
                break;

            case 'like':
                if ($column === "actioning_user") {
                    $query->whereIn(DB::raw("CONCAT(first_name, ' ', last_name)"), $value);
                }
                $query->where($column, 'like', '%' . $value . '%');
                break;

            case 'between':
                if ($column === "created_date") {
                    $column = 'audits.created_at';
                    if (isset($value[0])) {
                        $value[0] = Carbon::parse(str_replace('/', '-', $value[0]))->format('Y-m-d');
                    }
                    if (isset($value[1])) {
                        $value[1] = Carbon::parse(str_replace('/', '-', $value[1]))->addDays(1)->format('Y-m-d');
                    }
                }
                $query->whereBetween($column, $value);
                break;

            case 'whereIn':
                if ($column === "actioning_user") {
                    $query->whereIn(DB::raw("CONCAT(first_name, ' ', last_name)"), $value);
                } else if ($column === "approval_type") {
                    $query->whereIn(DB::raw("JSON_EXTRACT(audits.custom_values,'$.smartApprovalType')"), $value);
                } else
                    $query->whereIn($column, $value);
                break;

            case 'gt':
                if ($column === "created_date") {
                    $column = 'audits.created_at';
                    $value = array_map(function ($element) {
                        return Carbon::parse(str_replace('/', '-', $element))->format('Y-m-d');
                    }, $value);
                }
                str_contains($column, "created_at") ? $query->whereDate($column, '>', $value) : $query->where($column, '>', $value);

                break;

            case 'gte':
                if ($column === "created_date") {
                    $column = 'audits.created_at';
                    $value = array_map(function ($element) {
                        return Carbon::parse(str_replace('/', '-', $element))->format('Y-m-d');
                    }, $value);
                }
                str_contains($column, "created_at") ? $query->whereDate($column, '>=', $value) : $query->where($column, '>=', $value);
                break;

            case 'lt':
                if ($column === "created_date") {
                    $column = 'audits.created_at';
                    $value = array_map(function ($element) {
                        return Carbon::parse(str_replace('/', '-', $element))->format('Y-m-d');
                    }, $value);
                }
                str_contains($column, "created_at") ? $query->whereDate($column, '<', $value) : $query->where($column, '<', $value);
                break;

            case 'lte':
                if ($column === "created_date") {
                    $column = 'audits.created_at';
                    $value = array_map(function ($element) {
                        return Carbon::parse(str_replace('/', '-', $element))->format('Y-m-d');
                    }, $value);
                }
                str_contains($column, "created_at") ? $query->whereDate($column, '<=', $value) : $query->where($column, '<=', $value);
                break;
        }

        return $query;
    }

    private function getRelationshipQuery($query, $rule, $relationshipTable, $relationshipColumn, $value): ?Builder
    {
        return $query->whereHas($relationshipTable, function ($query) use ($rule, $relationshipColumn, $value) {
            return $this->switchFilters($query, $rule, $relationshipColumn, $value);
        });
    }

    public function scopeApplyFilter($query, ?string $filterString): ?Builder
    {
        if (!$filterString)
            return $query;

        foreach ($this->explodeFilters($filterString) as $filter) {
            $this->switchFilters($query, $filter["rule"], $filter["column"], $filter["value"]);
        }

        return $query;
    }
}
