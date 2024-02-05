<?php

namespace Laravel\Infrastructure\Mail\ActionButtons;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Infrastructure\Models\EmailBasedAction;

interface EmailBasedActionButtonServiceInterface
{
    public function getEmailTypeAction(string $slugId): Collection;
    public function createEmailBasedAction(array $params): array;

    public function buildPayload(array $params): array;

    public function getButtonWrapperHtml(array $params): string;

    public function encryptButton(array $emailData): array;
}
