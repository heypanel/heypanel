<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Acl\Role;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityProtection\EntityProtectionCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityProtection\WriteProtection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\IdField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ListField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use HeyPanel\Core\Framework\DataAbstractionLayer\Field\StringField;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldCollection;
use HeyPanel\Core\System\Integration\Aggregate\IntegrationRole\IntegrationRoleDefinition;
use HeyPanel\Core\System\Integration\IntegrationDefinition;
use HeyPanel\Core\System\User\UserDefinition;

class AclRoleDefinition extends EntityDefinition
{
    final public const PRIVILEGE_READ = 'read';
    final public const PRIVILEGE_CREATE = 'create';
    final public const PRIVILEGE_UPDATE = 'update';
    final public const PRIVILEGE_DELETE = 'delete';

    final public const PRIVILEGE_DEPENDENCE = [
        AclRoleDefinition::PRIVILEGE_READ => [],
        AclRoleDefinition::PRIVILEGE_CREATE => [AclRoleDefinition::PRIVILEGE_READ],
        AclRoleDefinition::PRIVILEGE_UPDATE => [AclRoleDefinition::PRIVILEGE_READ],
        AclRoleDefinition::PRIVILEGE_DELETE => [AclRoleDefinition::PRIVILEGE_READ],
    ];

    final public const ENTITY_NAME = 'acl_role';
    final public const ALL_ROLE_KEY = 'all';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return AclRoleCollection::class;
    }

    public function getEntityClass(): string
    {
        return AclRoleEntity::class;
    }

    public function getDefaults(): array
    {
        return ['privileges' => []];
    }

    protected function defineProtections(): EntityProtectionCollection
    {
        return new EntityProtectionCollection([
            new WriteProtection(Context::SYSTEM_SCOPE),
        ]);
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            new LongTextField('description', 'description'),
            (new ListField('privileges', 'privileges', StringField::class))->addFlags(new Required()),
            new DateTimeField('deleted_at', 'deletedAt'),
            new ManyToManyAssociationField('users', UserDefinition::class, AclUserRoleDefinition::class, 'acl_role_id', 'user_id'),
            new ManyToManyAssociationField('integrations', IntegrationDefinition::class, IntegrationRoleDefinition::class, 'acl_role_id', 'integration_id'),
        ]);
    }
}
