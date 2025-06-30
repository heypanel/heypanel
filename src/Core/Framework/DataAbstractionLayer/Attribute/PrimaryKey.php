<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class PrimaryKey
{
    public function __construct()
    {
    }
}
