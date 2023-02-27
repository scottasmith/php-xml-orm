<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Attributes;

use Attribute as PhpAttribute;

#[PhpAttribute(PhpAttribute::TARGET_CLASS)]
readonly class AttributeElementNS extends ElementNS
{
}
