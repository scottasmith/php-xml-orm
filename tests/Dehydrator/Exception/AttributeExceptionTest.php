<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Dehydrator\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Ssmiff\XmlOrm\Attributes\Element;
use Ssmiff\XmlOrm\Dehydrator\Exception\AttributeException;

class AttributeExceptionTest extends TestCase
{
    public function testNonUniqueReturnsExpectedException(): void
    {
        $exception = AttributeException::nonUnique([new Element(), new Element()]);

        $this->assertInstanceOf(AttributeException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertSame('More than one main attribute specified: Element, Element', $exception->getMessage());
    }
}
