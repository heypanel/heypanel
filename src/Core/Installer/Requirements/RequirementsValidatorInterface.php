<?php declare(strict_types=1);

namespace HeyPanel\Core\Installer\Requirements;

use HeyPanel\Core\Installer\Requirements\Struct\RequirementsCheckCollection;

/**
 * @internal
 */
interface RequirementsValidatorInterface
{
    public function validateRequirements(RequirementsCheckCollection $checks): RequirementsCheckCollection;
}
