<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Dehydrator\Exception;

use DOMException;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Ssmiff\XmlOrm\Dehydrator\Exception\ElementException;

class ElementExceptionTest extends TestCase
{
    public function testCreateElementFailedReturnsExpectedException(): void
    {
        $exception = ElementException::createElementFailed('Element', new DOMException('test'));

        $this->assertInstanceOf(ElementException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertInstanceOf(DOMException::class, $exception->getPrevious());
        $this->assertSame(
            'Failed to create element with from attribute (Element) with message: test',
            $exception->getMessage()
        );
    }

    public function testCreateElementValueFailedReturnsExpectedException(): void
    {
        $exception = ElementException::createElementValueFailed('Element', new ReflectionException('test'));

        $this->assertInstanceOf(ElementException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertInstanceOf(ReflectionException::class, $exception->getPrevious());
        $this->assertSame(
            'Failed to get value for element for attribute (Element) with message: test',
            $exception->getMessage()
        );
    }
}
