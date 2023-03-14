<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Dehydrator\Exception;

use Exception;

final class AttributeException extends Exception
{
    public static function nonUnique(array $attributes): self
    {
        $attributeNames = array_map(
            fn ($attribute) => array_slice(explode('\\', get_class($attribute)), -1)[0],
            $attributes
        );

        return new self('More than one main attribute specified: ' . implode(', ', $attributeNames));
    }
}
