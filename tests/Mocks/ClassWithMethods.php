<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Mocks;

use Ssmiff\XmlOrm\Attributes;

class ClassWithMethods
{
    public function __construct(
        #[Attributes\Element()]
        public string $prop1
    ) {
    }

    #[Attributes\ElementNS('http://example.com', 'tag1')]
    public function test1(): string
    {
        return 'abc';
    }

    #[Attributes\TextNode()]
    public function test2(): string
    {
        return 'start';
    }

    #[Attributes\EntityReference()]
    public function test3(): string
    {
        return 'copy';
    }

    #[Attributes\TextNode()]
    public function test4(): string
    {
        return 'end';
    }

    #[Attributes\ElementNS('http://example.com', 'tag2:e')]
    public function test5(): string
    {
        return 'abc';
    }
}
