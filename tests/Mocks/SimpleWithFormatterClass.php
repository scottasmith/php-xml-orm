<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrmTest\Mocks;

use Ssmiff\XmlOrm\Attributes;
use Ssmiff\XmlOrm\Attributes\Formatters;

#[Attributes\Element()]
class SimpleWithFormatterClass
{
    #[Formatters\DateTime(fromFormat: 'm/d/Y H:i:s')]
    private const DATE_TIME_TEST = '12/13/2023 15:23:23';

    #[Formatters\DateTime()]
    protected string $dateTimeTest = '2023-12-13 15:23:23';

    #[Formatters\DateTime('d-m-Y')]
    protected function testDateTime(): string
    {
        return '13-12-2023 15:23:23';
    }
}
