<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Mocks;

use Ssmiff\XmlOrm\Attributes;

class ClassWithProperties
{
    public string $prop1 = 'test';

    #[Attributes\Element()]
    public TestBackedEnum $prop3 = TestBackedEnum::VAL1;

    #[Attributes\Element('tag1')]
    public int $prop4 = 123;

    #[Attributes\CDATASection('tag2')]
    public string $prop5 = 'test';

    #[Attributes\TextNode()]
    public string $prop6 = 'start';

    #[Attributes\EntityReference()]
    public string $prop7 = 'copy';

    #[Attributes\TextNode()]
    public string $prop8 = 'end';

    #[Attributes\DocumentFragment()]
    public string $prop9 = '<copy/>';

    #[Attributes\Comment()]
    public string $prop10 = 'comment';

    #[Attributes\Comment()]
    public static string $prop12 = 'comment2';

    public function __construct(
        #[Attributes\Element()]
        public string $prop2,

        #[Attributes\Element()]
        public TestEnum $prop11
    ) {
    }
}
