<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Required
{
    public function __construct()
    {
    }
}
