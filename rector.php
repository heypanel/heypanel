<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/phpstan_dev/HeyPanel_Core_DevOps_StaticAnalyze_StaticAnalyzeKernelPhpstan_devDebugContainer.xml')
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/custom',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withFileExtensions(['php'])
    ->withImportNames()
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withSkip([
        __DIR__ . '/src/Core/Framework/Script/ServiceStubs.php',
        __DIR__ . '/src/Recovery',

        '**/vendor/*',
        '**/node_modules/*',
        '**/Resources/*',
    ]);
