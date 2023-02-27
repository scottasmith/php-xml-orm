<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Attributes;

use Attribute as PhpAttribute;
use Ssmiff\XmlOrm\Attributes\Interfaces\UniqueAttributeInterface;

#[PhpAttribute(
    PhpAttribute::TARGET_PROPERTY
    | PhpAttribute::TARGET_CLASS_CONSTANT
    | PhpAttribute::TARGET_METHOD
)]
readonly class TextNode implements UniqueAttributeInterface
{
    public function __construct(public ?string $data = null)
    {
    }
}
