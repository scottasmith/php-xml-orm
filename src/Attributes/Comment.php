<?php

declare(strict_types=1);

namespace Ssmiff\XmlOrm\Attributes;

use Attribute as PhpAttribute;
use Ssmiff\XmlOrm\Attributes\Interfaces\UniqueAttributeInterface;

#[PhpAttribute(
    PhpAttribute::TARGET_PROPERTY
    | PhpAttribute::TARGET_CLASS_CONSTANT
    | PhpAttribute::TARGET_METHOD
)]
readonly class Comment implements UniqueAttributeInterface
{
    public ?string $comment;

    public function __construct(?string $comment = null)
    {
        if (!$comment) {
            return;
        }

        $this->comment = str_replace(
            '--',
            '-' . chr(194) . chr(173) . '-',
            $comment
        );
    }
}
