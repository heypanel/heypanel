<?php declare(strict_types=1);

namespace HeyPanel\Core\System\StateMachine;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\StateMachine\Aggregation\StateMachineHistory\StateMachineHistoryDefinition;
use HeyPanel\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateDefinition;
use HeyPanel\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionDefinition;

class StateMachineDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'state_machine';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return StateMachineEntity::class;
    }

    public function getCollectionClass(): string
    {
        return StateMachineCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new StringField('technical_name', 'technicalName'))->addFlags(new Required()),
            (new TranslatedField('name'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            new TranslatedField('customFields'),

            (new OneToManyAssociationField('states', StateMachineStateDefinition::class, 'state_machine_id'))->addFlags(new ApiAware(), new CascadeDelete()),
            (new OneToManyAssociationField('transitions', StateMachineTransitionDefinition::class, 'state_machine_id'))->addFlags(new ApiAware(), new CascadeDelete()),
            new FkField('initial_state_id', 'initialStateId', StateMachineStateDefinition::class),

            (new TranslationsAssociationField(StateMachineTranslationDefinition::class, 'state_machine_id'))->addFlags(new CascadeDelete(), new Required()),
            new OneToManyAssociationField('historyEntries', StateMachineHistoryDefinition::class, 'state_machine_id'),
        ]);
    }
}
