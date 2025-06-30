<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Aggregate\CustomerGroupTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<CustomerGroupTranslationEntity>
 */
class CustomerGroupTranslationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'customer_group_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return CustomerGroupTranslationEntity::class;
    }
}
