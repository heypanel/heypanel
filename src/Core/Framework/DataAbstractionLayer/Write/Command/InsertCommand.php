<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Write\Command;

use HeyPanel\Core\Framework\Api\Acl\Role\AclRoleDefinition;

/**
 * @final
 */
class InsertCommand extends WriteCommand
{
    public function getPrivilege(): string
    {
        return AclRoleDefinition::PRIVILEGE_CREATE;
    }
}
