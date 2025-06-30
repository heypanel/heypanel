<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<CustomerEntity>
 */
class CustomerCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'customer_collection';
    }

    protected function getExpectedClass(): string
    {
        return CustomerEntity::class;
    }
}
