<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Dehydrator\Exception;

use DOMException;
use Exception;
use ReflectionException;

final class ElementException extends Exception
{
    public static function createElementFailed(string $attributeName, DOMException $exception): self
    {
        return new self(
            sprintf(
                'Failed to create element with from attribute (%s) with message: %s',
                $attributeName,
                $exception->getMessage()
            ),
            0,
            $exception
        );
    }

    public static function createElementValueFailed(string $attributeName, ReflectionException $exception): self
    {
        return new self(
            sprintf(
                'Failed to get value for element for attribute (%s) with message: %s',
                $attributeName,
                $exception->getMessage()
            ),
            0,
            $exception
        );
    }
}
