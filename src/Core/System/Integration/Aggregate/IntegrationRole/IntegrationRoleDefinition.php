<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Integration\Aggregate\IntegrationRole;

use HeyPanel\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\FkField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use HeyPanel\Core\System\Integration\IntegrationDefinition;

class IntegrationRoleDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 'integration_role';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function since(): ?string
    {
        return '6.3.3.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('integration_id', 'integrationId', IntegrationDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('acl_role_id', 'aclRoleId', AclRoleDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('integration', 'integration_id', IntegrationDefinition::class, 'id', false),
            new ManyToOneAssociationField('role', 'acl_role_id', AclRoleDefinition::class, 'id', false),
        ]);
    }
}
