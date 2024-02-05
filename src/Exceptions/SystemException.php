<?php

namespace Laravel\Infrastructure\Exceptions;

use Illuminate\Http\Request;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;

class SystemException extends \Exception
{
    public $errorData;
    public $event_type;
    public $message;
    protected string $defaultMessage;
    protected bool $showDefaultMessageByEnv = false;

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setMessage($message);
    }

    public function setMessage(string $message = "")
    {
        $this->message = $message;
    }

    public function report(?Request $request): void
    {
        $thrownException = $request ? $this : $this->getPrevious(); // if request is null then its system exception otherwise our own thrown exception
        ExceptionReporterServiceFacade::report($thrownException, $this->errorData);
    }

    public function setErrorData(array|null $data): self
    {
        $this->errorData = $data;
        return $this;
    }

    public function setEventType(string|null $eventName): self
    {
        $this->event_type = $eventName;
        return $this;
    }
}
