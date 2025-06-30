<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Aggregate\CustomerGroupTranslation;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\TranslationEntity;

class CustomerGroupTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;
}
