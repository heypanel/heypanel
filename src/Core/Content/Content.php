<?php declare(strict_types=1);

namespace HeyPanel\Core\Content;

use HeyPanel\Core\Content\Mail\MailerConfigurationCompilerPass;
use HeyPanel\Core\Framework\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
class Content extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('rule.xml');
        $loader->load('flow.xml');
        $loader->load('post.xml');
        $loader->load('media_path.xml');
        $loader->load('media.xml');
        $loader->load('cms.xml');
        $loader->load('category.xml');
        $loader->load('sitemap.xml');
        $loader->load('landing_page.xml');
        $loader->load('import_export.xml');
        $loader->load('mail_template.xml');

        $container->addCompilerPass(new MailerConfigurationCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 0);
    }
}
