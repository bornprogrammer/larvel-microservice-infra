<?php

namespace Laravel\Infrastructure\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Facades\SearchFilterConfigRepositoryFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Helpers\RoleHelper;

class FilterDataBuilderService extends BaseService
{
  public function build(string $searchFilterConfigName, Collection $data, callable $callback = null, array $skipColumnNames = []): array
  {
    $searchFilterConfigDetails = SearchFilterConfigRepositoryFacade::getNBuildSearchFilterConfig($searchFilterConfigName);
    $filterData = $this->buildFilterNodes($searchFilterConfigDetails);
    if ($data->isNotEmpty() && ArrayHelper::isArrayValid($filterData)) {
      foreach ($data as $item) {
        foreach ($searchFilterConfigDetails as $field => $fieldDetail) {
          if (isset($filterData[$field])) {
            if ($this->isFieldToHaveChildren($fieldDetail)) {
              $filterNodeChild = $this->buildFilterNodeChild($fieldDetail, $item, $callback);
              if (ArrayHelper::isArrayValid($filterNodeChild)) {
                $filterData[$field]['children'][$filterNodeChild['value']] = $filterNodeChild;
              }
            }
          }
        }
      }
      $emptyChildren = [];
      foreach ($filterData as $field => &$filterNode) { // removing keys which is field name used for searching
        $filterNode['children'] = array_values($filterNode['children']);
        if (!ArrayHelper::isArrayValid($filterNode['children']) && in_array($filterNode['key'], $skipColumnNames)) {
          $emptyChildren[$field] = [];
        }
      }
      $filterData = array_diff_key($filterData, $emptyChildren);
    }
    return array_values($filterData);
  }

  protected function isFieldToHaveChildren(array $fieldDetails): bool
  {
    return $fieldDetails['filter_data_keys'] !== "none";
  }

  protected function buildFilterNodes(array $searchFilterConfigDetails): array
  {
    $filterData = [];
    if (ArrayHelper::isArrayValid($searchFilterConfigDetails)) {
      foreach ($searchFilterConfigDetails as $field => $fieldDetails) {
        if (in_array($fieldDetails['type'], ["all", "filter"]) && (in_array(RoleHelper::getRoleName(), explode(",", $fieldDetails["role"])) || is_null($fieldDetails["role"]))) {
          $filterData[$field] = $this->buildFilterNodeSchema($field, $fieldDetails);
        }
      }
    }
    return $filterData;
  }

  protected function setFilterNodeChildrenLabelNValue(string $labelField, string $valueField, Model $collectionItem): array
  {
    return ['label' => $collectionItem->{$labelField}, 'value' => $collectionItem->{$valueField}];
  }

  protected function buildFilterNodeChild(array $fieldDetail, Model $collectionItem, callable $callback = null): array
  {
    $filterNodeChildren = [];
    $filterDataKeys = $fieldDetail['filter_data_keys']; // none mean child will not have any data,null means bind the data for same column as mentioned in field, json string like {"label":"column_name","value":"column_name"} means keep the label and value column name,callback means calliee will pass a callback for child data manipulation
    $field = $fieldDetail['field_name'];
    if ($filterDataKeys === "callback") {
      $filterNodeChildren = $callback($field, $collectionItem, $fieldDetail);
    } else if (is_null($filterDataKeys)) {
      if (!empty($collectionItem[$field])) {
        $filterNodeChildren = $this->setFilterNodeChildrenLabelNValue($field, $field, $collectionItem);
      }
    } else {
      $labelValueArray = json_decode($filterDataKeys, true);
      $filterNodeChildren = $this->setFilterNodeChildrenLabelNValue($labelValueArray['label'], $labelValueArray['value'], $collectionItem);
    }
    return $filterNodeChildren;
  }

  protected function buildFilterNodeSchema(string $field, array $fieldDetail): array
  {
    $schema = ['key' => $field, 'label' => $fieldDetail['label'], 'queryType' => $fieldDetail['operator'], 'enableSearch' => $fieldDetail['is_searchable'], 'children' => []];
    if ($fieldDetail['data_type'] === 'date') {
      $schema['type'] = $fieldDetail['data_type'];
    }
    return $schema;
  }
}
