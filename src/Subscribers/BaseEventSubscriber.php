<?php

namespace Laravel\Infrastructure\Subscribers;

use Laravel\Infrastructure\Helpers\ArrayHelper;

class BaseEventSubscriber
{
  protected array $subscribers = [];

  public function subscribe($laravelDispatchedEvent)
  {
    if (ArrayHelper::isArrayValid($this->subscribers)) {
      foreach ($this->subscribers as $event => $listeners) {
        foreach ($listeners as $listener) {
          $laravelDispatchedEvent->listen($event, [$listener, 'handle']);
        }
      }
    }
  }
}

// $event->listen(CompanyDeleted::class, [DeleteCompanyFromCodat::class, 'handle']);

//       $event->listen(CompanyDeleted::class, [ResetCategoryMapping::class, 'handle']);

//       $event->listen(CompanyDeleted::class, [DeleteRecordsFromLastDisconnectedIntegration::class, 'handle']);