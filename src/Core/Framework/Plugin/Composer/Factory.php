<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Composer;

use Composer\Composer;
use Composer\Factory as ComposerFactory;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use HeyPanel\Core\DevOps\Environment\EnvironmentHelper;
use HeyPanel\Core\Kernel;

class Factory
{
    public static function createComposer(string $composerJsonDir, ?IOInterface $composerIO = null): Composer
    {
        if ($composerIO === null) {
            $composerIO = new NullIO();
        }

        $composerJsonPath = $composerJsonDir . '/composer.json';

        $json = json_decode((string) file_get_contents($composerJsonPath), true, \JSON_THROW_ON_ERROR);

        $previousRootVersion = EnvironmentHelper::hasVariable('COMPOSER_ROOT_VERSION') ? EnvironmentHelper::getVariable('COMPOSER_ROOT_VERSION') : null;

        // This is a workaround to make sure that the heypanel platform package has the correct version
        if (($json['name'] ?? '') === 'heypanel/platform' && !isset($json['version']) && !EnvironmentHelper::hasVariable('COMPOSER_ROOT_VERSION')) {
            $_SERVER['COMPOSER_ROOT_VERSION'] = Kernel::HEYADMIN_FALLBACK_VERSION;
        }

        $composer = (new ComposerFactory())->createComposer(
            $composerIO,
            $composerJsonPath,
            false,
            $composerJsonDir
        );

        if ($previousRootVersion === null) {
            unset($_SERVER['COMPOSER_ROOT_VERSION']);
        } else {
            $_SERVER['COMPOSER_ROOT_VERSION'] = $previousRootVersion;
        }

        return $composer;
    }
}
