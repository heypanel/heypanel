<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Field;

interface StorageAware
{
    public function getStorageName(): string;
}
