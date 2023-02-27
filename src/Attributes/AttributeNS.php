<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Attributes;

use Attribute as PhpAttribute;
use Ssmiff\XmlOrm\Attributes\Interfaces\OptionalAttributeInterface;

#[PhpAttribute(
    PhpAttribute::TARGET_CLASS
    | PhpAttribute::TARGET_PROPERTY
    | PhpAttribute::TARGET_CLASS_CONSTANT
    | PhpAttribute::TARGET_METHOD
    | PhpAttribute::IS_REPEATABLE
)]
readonly class AttributeNS implements OptionalAttributeInterface
{
    public function __construct(
        public string $namespace,
        public ?string $tagName = null,
        public ?string $value = null
    ) {
    }
}
