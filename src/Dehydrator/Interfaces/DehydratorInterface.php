<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Dehydrator\Interfaces;

interface DehydratorInterface
{
    public function dehydrate(): void;
}
