<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Interfaces;

use SimpleXMLElement;

interface DehydratorInterface
{
    public function dehydrate(object $class): SimpleXMLElement;
}
