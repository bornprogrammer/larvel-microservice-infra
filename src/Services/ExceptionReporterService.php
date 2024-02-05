<?php

namespace Laravel\Infrastructure\Services;

use Laravel\Infrastructure\Log\Logger;
use Laravel\Infrastructure\Models\SystemLog;
use Laravel\Infrastructure\Helpers\RoleHelper;
use Laravel\Infrastructure\Helpers\EnvironmentHelper;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;

class ExceptionReporterService extends BaseService
{
    public function report(\Throwable $throwable, ?array $errorData = null): void
    {
        if (EnvironmentHelper::isLocal()) {
            Logger::info($this->buildExceptionMessage($throwable));
        } else {
            $this->saveToDB($throwable, $errorData);
            Logger::error($throwable->getMessage(), $errorData ?? []);
        }
    }
    protected function buildExceptionMessage(\Throwable $throwable): string
    {
        $message = $throwable->getMessage();
        $fileName = $throwable->getFile();
        $lineNo = $throwable->getLine();
        $userOrgId = RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken() ?? null;
        $orgId = RequestSessionFacade::getOrgIdFromQueryStrElseFromToken() ?? null;
        $roleName = RoleHelper::getRoleName() ?? null;
        $stackTrace = $throwable->getTraceAsString();
        $message = <<<EOD
        "-----------------Exception thrown-----------------"
        Stack Trace :---->  $stackTrace
        User ORG ID :----> $userOrgId
        ORG ID    :----> $orgId
        Role Name :----> $roleName
        Message   :----> $message
        File Path :---->  $fileName
        Line No   :---->  $lineNo 
        "-----------------Exception End-----------------"
EOD;
        return $message;
    }

    protected function buildExceptionMessagePayload(\Throwable $throwable, ?array $errorData = null): array
    {
        $errorData = ['data' => $errorData, 'stack_trace' => $throwable->getTraceAsString()];
        $errorData  = json_encode($errorData);
        $requestPayload = request()->all();
        $requestPayload = ArrayHelper::isArrayValid($requestPayload) ? json_encode($requestPayload) : null;
        $exceptionPayload = [
            "user_org_id" => RequestSessionFacade::getUserOrgId(),
            "org_id" => RequestSessionFacade::getOrgId(),
            "role_name" => RoleHelper::getRoleName(),
            "exception_type" => get_class(),
            "file_name" => $throwable->getFile(),
            "line_number" => $throwable->getLine(),
            "error_message" => $throwable->getMessage(),
            "event_type" => $throwable->event_type ?? "request",
            "error_data" => $errorData,
            "user_name" => RequestSessionFacade::getUserFullName() ?? null,
            "organization_name" => RequestSessionFacade::getOrgName() ?? null,
            "api" => request()->getRequestUri(),
            "api_payload" => $requestPayload,
            "ms_name" => config("app.app_name") ?? null
        ];
        return $exceptionPayload;
    }

    public function saveToDB(\Throwable $throwable, ?array $errorData = null): void
    {
        try {
            $exceptionPayload = $this->buildExceptionMessagePayload($throwable, $errorData);
            SystemLog::create($exceptionPayload);
        } catch (\Exception $exception) {
            // left empty not to go in recurive loop
        }
    }
}
