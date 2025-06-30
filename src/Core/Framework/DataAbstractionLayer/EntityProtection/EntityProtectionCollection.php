<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\EntityProtection;

use HeyPanel\Core\Framework\Struct\Collection;

/**
 * @extends Collection<EntityProtection>
 */
class EntityProtectionCollection extends Collection
{
    /**
     * @param EntityProtection $element
     */
    public function add($element): void
    {
        $this->set($element::class, $element);
    }

    /**
     * @param string|int $key
     * @param EntityProtection $element
     */
    public function set($key, $element): void
    {
        parent::set($element::class, $element);
    }

    public function getApiAlias(): string
    {
        return 'dal_protection_collection';
    }
}
