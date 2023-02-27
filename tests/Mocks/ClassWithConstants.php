<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Mocks;

use Ssmiff\XmlOrm\Attributes;

#[Attributes\ElementNS('http://example1.org', 'el')]
class ClassWithConstants
{
    public const const1 = 'test';

    #[Attributes\Element()]
    public const const2 = TestEnum::VAL1;

    #[Attributes\Element()]
    public const const3 = TestBackedEnum::VAL1;

    #[Attributes\ElementNs('http://example2.org')]
    public const const4 = 123;

    #[Attributes\ElementNs('http://example3.org', 'tag5')]
    public const const5 = 'test2';

    #[Attributes\Element('tag1')]
    #[Attributes\Attribute('attr1', 'test')]
    public const const6 = 'val1';

    #[Attributes\AttributeNs('http://example4.org', 'r:attr1', 'attrVal')]
    public const const7 = 'val2';

    #[Attributes\CDATASection('tag2')]
    public const const8 = 'cdata-eg';

    #[Attributes\TextNode()]
    public const const9 = 'start';

    #[Attributes\EntityReference()]
    public const const10 = 'copy';

    #[Attributes\TextNode()]
    public const const11 = 'end';

    #[Attributes\DocumentFragment()]
    public const const12 = '<copy/>';

    #[Attributes\Comment()]
    public const const13 = 'comment';
}
