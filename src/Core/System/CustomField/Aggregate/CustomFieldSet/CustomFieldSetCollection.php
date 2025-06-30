<?php declare(strict_types=1);

namespace HeyPanel\Core\System\CustomField\Aggregate\CustomFieldSet;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<CustomFieldSetEntity>
 */
class CustomFieldSetCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'custom_field_set_collection';
    }

    protected function getExpectedClass(): string
    {
        return CustomFieldSetEntity::class;
    }
}
