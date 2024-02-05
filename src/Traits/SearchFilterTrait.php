<?php

namespace Laravel\Infrastructure\Traits;

use \Illuminate\Database\Eloquent\Builder;
use Laravel\Infrastructure\Constants\SearchFilterOperatorTypeConstant;
use Laravel\Infrastructure\Facades\SearchFilterConfigRepositoryFacade;
use Laravel\Infrastructure\Facades\SearchFilterServiceFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Helpers\RoleHelper;

trait SearchFilterTrait
{
  public function scopeApplyFilter($query, string $searchFilterName, callable|null $queryCallback = null): ?Builder
  {
    $filterString = request()->query("filter");
    if ($filterString) {
      $searchFilterConfig = $this->getSearchFilterConfig($searchFilterName);
      if (ArrayHelper::isArrayValid($searchFilterConfig)) {
        $filters = SearchFilterServiceFacade::explodeFilters($filterString);
        foreach ($filters as $filter) {
          if (in_array($searchFilterConfig[$filter["column"]]['type'], ["all", "filter"])) {
            SearchFilterServiceFacade::buildQueryForFilter($query, $filter["rule"], $filter["column"], $filter["value"], $searchFilterConfig, $queryCallback);
          }
        }
      }
    }
    return $query;
  }

  public function scopeApplySearch($query, string $searchFilterName, callable|null $queryCallback = null): ?Builder
  {
    $searchString = request()->query("search");
    if (!empty($searchString)) {
      $searchFilterConfig = $this->getSearchFilterConfig($searchFilterName);
      if (ArrayHelper::isArrayValid($searchFilterConfig)) {
        $query->where(function ($query) use ($searchFilterConfig, $searchString, $queryCallback) {
          foreach ($searchFilterConfig as $searchFilterDetail) {
            if (in_array($searchFilterDetail['type'], ['all', 'search']) && (in_array(RoleHelper::getRoleName(), explode(",", $searchFilterDetail["role"])) || is_null($searchFilterDetail["role"]))) {
              $query->orWhere(function ($query) use ($searchFilterDetail, $searchString, $searchFilterConfig, $queryCallback) {
                SearchFilterServiceFacade::buildQueryForSearch($query, SearchFilterOperatorTypeConstant::OPERATOR_TYPE_LIKE, $searchFilterDetail['field_name'], $searchString, $searchFilterConfig, $queryCallback);
              });
            }
          }
        });
      }
    }
    return $query;
  }

  protected function getSearchFilterConfig(string $searchFilterName): array
  {
    return SearchFilterConfigRepositoryFacade::getNBuildSearchFilterConfig($searchFilterName);
  }
}
