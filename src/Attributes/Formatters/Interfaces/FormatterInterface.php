<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Attributes\Formatters\Interfaces;

interface FormatterInterface
{
    public function format(mixed $value): string;
}
