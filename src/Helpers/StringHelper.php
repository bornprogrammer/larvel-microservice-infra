<?php

namespace Laravel\Infrastructure\Helpers;

class StringHelper
{
    public function isStringEmpty(?string $val): bool
    {
        return (bool) $val;
    }

    public static function replaceCharWithSpace(?string $val, ?string $character): string
    {
        $val = str_replace($character, ' ', $val);
        return $val;
    }

    public static function replaceInterpolationSyntaxFromHTMLString(array $findAndReplace, string $htmlString): string
    {
        $values = array_values($findAndReplace);
        $keys = array_map(function ($key) {
            return "{" . $key . "}";
        }, array_keys($findAndReplace));
        $replaceHTMLString = str_replace($keys, $values, $htmlString);
        return $replaceHTMLString;
    }
}
