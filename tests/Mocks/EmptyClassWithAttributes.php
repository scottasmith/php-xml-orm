<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Mocks;

use Ssmiff\XmlOrm\Attributes;

#[Attributes\Attribute('attr1', 'val1')]
#[Attributes\Attribute('attr2', 'val2')]
class EmptyClassWithAttributes
{
}
