<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Search;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use HeyPanel\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\Exception\ApiProtectionException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Exception\RuntimeFieldInCriteriaException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Field;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiCriteriaAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;

/**
 * @final
 */
class ApiCriteriaValidator
{
    /**
     * @internal
     */
    public function __construct(private readonly DefinitionInstanceRegistry $registry)
    {
    }

    public function validate(string $entity, Criteria $criteria, Context $context): void
    {
        $definition = $this->registry->getByEntityName($entity);

        foreach ($criteria->getAllFields() as $accessor) {
            $fields = EntityDefinitionQueryHelper::getFieldsOfAccessor($definition, $accessor);

            foreach ($fields as $field) {
                if (!$field instanceof Field) {
                    continue;
                }

                if ($field->getFlag(ApiCriteriaAware::class)) {
                    continue;
                }

                $flag = $field->getFlag(ApiAware::class);

                if ($flag === null) {
                    throw new ApiProtectionException($accessor);
                }

                if (!$flag->isSourceAllowed($context->getSource()::class)) {
                    throw new ApiProtectionException($accessor);
                }

                $runtime = $field->getFlag(Runtime::class);

                if ($runtime !== null) {
                    throw new RuntimeFieldInCriteriaException($accessor);
                }
            }
        }
    }
}
