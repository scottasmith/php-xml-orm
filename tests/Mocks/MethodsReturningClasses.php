<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Mocks;

use Ssmiff\XmlOrm\Attributes;

#[Attributes\ElementNS('http://example.com', 'e:test1')]
#[Attributes\AttributeNS('http://example2.com', 'e1:t', 'foo')]
#[Attributes\AttributeNS('http://example3.com', 'e2:t', 'bar')]
class MethodsReturningClasses
{
    #[Attributes\Attribute('foo', 'foo')]
    public function getSimpleElement(): SimpleNsEmptyClass
    {
        return new SimpleNsEmptyClass();
    }

    #[Attributes\ElementNs('http://example2.com', 'foo')]
    public function getSimpleElement2(): callable
    {
        return fn () => 'test';
    }
}
