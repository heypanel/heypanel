<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Field;

use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\FieldResolver\OneToManyAssociationFieldResolver;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer\OneToManyAssociationFieldSerializer;

class OneToManyAssociationField extends AssociationField
{
    protected string $localField;

    public function __construct(
        string $propertyName,
        string $referenceClass,
        string $referenceField,
        string $localField = 'id'
    ) {
        parent::__construct($propertyName);
        $this->localField = $localField;
        $this->referenceField = $referenceField;
        $this->referenceClass = $referenceClass;
    }

    public function getLocalField(): string
    {
        return $this->localField;
    }

    protected function getSerializerClass(): string
    {
        return OneToManyAssociationFieldSerializer::class;
    }

    protected function getResolverClass(): ?string
    {
        return OneToManyAssociationFieldResolver::class;
    }
}
