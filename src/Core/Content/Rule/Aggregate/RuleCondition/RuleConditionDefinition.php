<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Rule\Aggregate\RuleCondition;

use HeyPanel\Core\Content\Rule\RuleDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ChildrenAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IntField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\JsonField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ParentAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ParentFkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;

class RuleConditionDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'rule_condition';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return RuleConditionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return RuleConditionCollection::class;
    }

    protected function getParentDefinitionClass(): ?string
    {
        return RuleDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('type', 'type'))->addFlags(new Required()),
            (new FkField('rule_id', 'ruleId', RuleDefinition::class))->addFlags(new Required()),
            new ParentFkField(self::class),
            new JsonField('value', 'value'),
            new IntField('position', 'position'),
            new ManyToOneAssociationField('rule', 'rule_id', RuleDefinition::class, 'id'),
            new ParentAssociationField(self::class, 'id'),
            new ChildrenAssociationField(self::class),
            new CustomFields(),
        ]);
    }
}
