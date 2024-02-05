<?php

namespace Laravel\Infrastructure\Mail;

use Laravel\Infrastructure\Helpers\StringHelper;

abstract class BaseEmailTemplateBuilder
{
    protected ?array $data;
    public function replaceInterpolationSyntaxAndAppend(array $emailTemplateDate, string $htmlTemplateString): self
    {
        $this->data[] = StringHelper::replaceInterpolationSyntaxFromHTMLString($emailTemplateDate, $htmlTemplateString);
        return $this;
    }

    public function append(?string $htmlTemplateString): self
    {
        $this->data[] = $htmlTemplateString ?? "";
        return $this;
    }
    public function build(): string
    {
        return implode("", $this->data);
    }
}
