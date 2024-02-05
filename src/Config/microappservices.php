<?php

return [
    \Laravel\Infrastructure\Constants\EnvironmentConstants::ENVIRONMENT_LOCAL => [
        "base_url" => config("app.localhost"),
        "services" => []
    ],
];
