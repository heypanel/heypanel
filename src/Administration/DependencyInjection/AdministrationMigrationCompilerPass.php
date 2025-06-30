<?php declare(strict_types=1);

namespace HeyPanel\Administration\DependencyInjection;

use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\AbstractMigrationReplacementCompilerPass;

class AdministrationMigrationCompilerPass extends AbstractMigrationReplacementCompilerPass
{
    protected function getMigrationPath(): string
    {
        return \dirname(__DIR__);
    }

    protected function getMigrationNamespacePart(): string
    {
        return 'Administration';
    }
}
