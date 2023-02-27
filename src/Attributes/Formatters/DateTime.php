<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Attributes\Formatters;

use Attribute;
use DateTime as PhpDateTime;
use DateTimeInterface;
use Exception;
use Ssmiff\XmlOrm\Attributes\Formatters\Interfaces\FormatterInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS_CONSTANT | Attribute::TARGET_METHOD)]
final class DateTime implements FormatterInterface
{
    public function __construct(
        public string $format = DateTimeInterface::ATOM,
        public ?string $fromFormat = null
    ) {
    }

    /**
     * @throws Exception
     */
    public function format(mixed $value): string
    {
        if (!$value instanceof DateTime && !is_string($value)) {
            return $value;
        }

        if (is_string($value) && !$this->fromFormat) {
            $value = new PhpDateTime($value);
        } elseif (is_string($value)) {
            $value = PhpDateTime::createFromFormat($this->fromFormat, $value);
        }

        /** @var DateTimeInterface $value */
        return $value->format($this->format);
    }
}
