<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Attributes;

use Attribute as PhpAttribute;
use Ssmiff\XmlOrm\Attributes\Interfaces;

#[PhpAttribute(
    PhpAttribute::TARGET_CLASS
    | PhpAttribute::TARGET_PROPERTY
    | PhpAttribute::TARGET_CLASS_CONSTANT
    | PhpAttribute::TARGET_METHOD
)]
readonly class Element implements
    Interfaces\CanHaveXmlAttributesInterface,
    Interfaces\UniqueAttributeInterface
{
    public function __construct(public ?string $tagName = null)
    {
    }
}
