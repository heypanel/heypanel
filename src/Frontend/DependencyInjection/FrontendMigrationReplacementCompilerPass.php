<?php declare(strict_types=1);

namespace HeyPanel\Frontend\DependencyInjection;

use HeyPanel\Core\Framework\DependencyInjection\CompilerPass\AbstractMigrationReplacementCompilerPass;

class FrontendMigrationReplacementCompilerPass extends AbstractMigrationReplacementCompilerPass
{
    protected function getMigrationPath(): string
    {
        return \dirname(__DIR__);
    }

    protected function getMigrationNamespacePart(): string
    {
        return 'Frontend';
    }
}
