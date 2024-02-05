<?php

namespace Laravel\Infrastructure\Log;


use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Helpers\DateHelper;

class JsonFormatter extends \Monolog\Formatter\JsonFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        $record = [
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
            'when' => $record['datetime']->format('l jS F Y h:i:s A'),
            'application' => config("app.app_name"),
            'host' => request()->server('SERVER_ADDR'),
            'remote-addrress' => request()->server('REMOTE_ADDR'),
            'level' => $record['level_name'],
            'message' => $record['message'],
            'org_id' => RequestSessionFacade::getOrgIdFromQueryStrElseFromToken(),
            'user_org_id' => RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken(),
            "extra" => $record['extra'],
            "context" => $record['context'],
        ];

        $json = $this->toJson($this->normalize($record), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }
}
