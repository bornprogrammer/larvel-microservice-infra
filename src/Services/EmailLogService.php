<?php

namespace Laravel\Infrastructure\Services;

use Laravel\Infrastructure\Constants\EnvironmentConstants;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Helpers\EnvironmentHelper;
use Laravel\Infrastructure\Log\Logger;
use Laravel\Infrastructure\Repositories\EmailLogsRepository;

class EmailLogService extends BaseService
{
    protected $emailLogsRepository;

    public function __construct(EmailLogsRepository $emailLogsRepository)
    {
        $this->emailLogsRepository = $emailLogsRepository;
    }

    public function createEmailLog($userId, $action, $link, $emailData)
    {
        return $this->emailLogsRepository->createEmailLog($userId, $action, $link, $emailData);
    }
}
