<?php declare(strict_types=1);

use Composer\InstalledVersions;

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\UX\TwigComponent\TwigComponentBundle::class => ['all' => true],
    HeyPanel\Core\Profiling\Profiling::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
    HeyPanel\Core\Framework\Framework::class => ['all' => true],
    HeyPanel\Core\System\System::class => ['all' => true],
    HeyPanel\Core\Content\Content::class => ['all' => true],
    HeyPanel\Core\DevOps\DevOps::class => ['all' => true],
    HeyPanel\Core\Maintenance\Maintenance::class => ['all' => true],
    HeyPanel\Administration\Administration::class => ['all' => true],
    HeyPanel\Frontend\Frontend::class => ['all' => true],
];

if (InstalledVersions::isInstalled('symfony/web-profiler-bundle')) {
    $bundles[Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class] = ['dev' => true, 'test' => true, 'phpstan_dev' => true];
}

return $bundles;
