<?php declare(strict_types=1);

namespace HeyPanel\Core\Installer;

use Composer\InstalledVersions;
use HeyPanel\Core\DevOps\Environment\EnvironmentHelper;
use HeyPanel\Core\Framework\Util\VersionParser;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @internal
 */
class InstallerKernel extends HttpKernel
{
    use MicroKernelTrait;

    private readonly string $heypanelVersion;

    private readonly ?string $heypanelVersionRevision;

    public function __construct(
        string $environment,
        bool $debug
    ) {
        parent::__construct($environment, $debug);

        // @codeCoverageIgnoreStart - not testable, as static calls cannot be mocked
        if (InstalledVersions::isInstalled('heypanel/platform')) {
            $version = InstalledVersions::getVersion('heypanel/platform')
                . '@' . InstalledVersions::getReference('heypanel/platform');
        } else {
            $version = InstalledVersions::getVersion('heypanel/core')
                . '@' . InstalledVersions::getReference('heypanel/core');
        }
        // @codeCoverageIgnoreEnd

        $version = VersionParser::parseHeyPanelVersion($version);
        $this->heypanelVersion = $version['version'];
        $this->heypanelVersionRevision = $version['revision'];
    }

    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        parent::boot();
        $this->ensureComposerHomeVarIsSet();
    }

    /**
     * @return \Generator<BundleInterface>
     */
    public function registerBundles(): \Generator
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new Installer();
    }

    public function getProjectDir(): string
    {
        $r = new \ReflectionObject($this);

        $file = $r->getFileName();
        if (!$file || !\is_file($file)) {
            throw new \LogicException(\sprintf('Cannot auto-detect project dir for kernel of class "%s".', $r->name));
        }

        $dir = $rootDir = \dirname($file);
        while (!\is_dir($dir . '/vendor')) {
            if ($dir === \dirname($dir)) {
                return $rootDir;
            }
            $dir = \dirname($dir);
        }

        return $dir;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();

        return array_merge(
            $parameters,
            [
                'kernel.heypanel_version' => $this->heypanelVersion,
                'kernel.heypanel_version_revision' => $this->heypanelVersionRevision,
                'kernel.secret' => 'noSecr3t',
            ]
        );
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        // use hard coded default config for loaded bundles
        $loader->load(__DIR__ . '/../Framework/Resources/config/packages/installer.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__ . '/Resources/config/routes.xml');
    }

    /**
     * We check the requirements via composer, and composer will fail if the composer home is not set
     */
    private function ensureComposerHomeVarIsSet(): void
    {
        if (!EnvironmentHelper::getVariable('COMPOSER_HOME')) {
            // The same location is also used in EnvConfigWriter and SystemSetupCommand
            $fallbackComposerHome = $this->getProjectDir() . '/var/cache/composer';
            $_ENV['COMPOSER_HOME'] = $_SERVER['COMPOSER_HOME'] = $fallbackComposerHome;
        }
    }
}
