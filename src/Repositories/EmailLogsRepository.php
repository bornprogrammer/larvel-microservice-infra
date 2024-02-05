<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Laravel\Infrastructure\Repositories;

use Laravel\Infrastructure\Models\EmailLog;
use Laravel\Infrastructure\Log\Logger;
use App\DTOs\UpdateAddressDTO;
use Laravel\Infrastructure\Repositories\BaseRepository;
use Laravel\Infrastructure\Exceptions\BadRequestException;

class EmailLogsRepository extends BaseRepository
{
    public function createEmailLog(string $userId, string $action, string $link, array $emailData)
    {
        return  EmailLog::create(
            [
                'user_id' => $userId,
                'action' => $action,
                'link' => $link,
                'payload' => json_encode($emailData),
                'email_sent_at' => now()
            ]
        );
    }
}
