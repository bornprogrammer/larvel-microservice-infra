<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Laravel\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Infrastructure\Repositories\BaseRepository;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Models\SearchFilterConfig;

class SearchFilterConfigRepository extends BaseRepository
{
  public function getSearchFilterConfig(string $searchFilterName): Collection
  {
    return SearchFilterConfig::where("name", "LIKE", "%{$searchFilterName}%")->orderBy('search_filter_configs.sequence_filter_by')->get();
  }

  public function getNBuildSearchFilterConfig(string $searchFilterName): array
  {
    $searchFilterFieldDetailArray = [];
    $searchFilterFieldDetails = $this->getSearchFilterConfig($searchFilterName);
    if ($searchFilterFieldDetails->isNotEmpty()) {
      foreach ($searchFilterFieldDetails as $searchFilterField) {
        $searchFilterFieldDetailArray[$searchFilterField->field_name] = ArrayHelper::convertObjectToArray($searchFilterField);
      }
    }
    return $searchFilterFieldDetailArray;
  }
}
