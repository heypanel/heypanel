<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DependencyInjection\CompilerPass;

use HeyPanel\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use HeyPanel\Core\Content\Flow\Dispatching\Storer\FlowStorer;
use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use HeyPanel\Core\Content\Sitemap\Provider\AbstractUrlProvider;
use HeyPanel\Core\Framework\Adapter\Filesystem\Adapter\AdapterFactoryInterface;
use HeyPanel\Core\Framework\Adapter\Twig\NamespaceHierarchy\TemplateNamespaceHierarchyBuilderInterface;
use HeyPanel\Core\Framework\DataAbstractionLayer\BulkEntityExtension;
use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityExtension;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use HeyPanel\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;
use HeyPanel\Core\Framework\Rule\Rule;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutoconfigureCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(EntityDefinition::class)
            ->addTag('heypanel.entity.definition');

        $container
            ->registerForAutoconfiguration(EntityExtension::class)
            ->addTag('heypanel.entity.extension');

        $container
            ->registerForAutoconfiguration(Rule::class)
            ->addTag('heypanel.rule.definition');

        $container
            ->registerForAutoconfiguration(CmsElementResolverInterface::class)
            ->addTag('heypanel.cms.data_resolver');

        $container
            ->registerForAutoconfiguration(FlowStorer::class)
            ->addTag('flow.storer');

        $container
            ->registerForAutoconfiguration(AbstractUrlProvider::class)
            ->addTag('heypanel.sitemap_url_provider');

        $container
            ->registerForAutoconfiguration(BulkEntityExtension::class)
            ->addTag('heypanel.bulk.entity.extension');

        $container
            ->registerForAutoconfiguration(SeoUrlRouteInterface::class)
            ->addTag('heypanel.seo_url.route');

        $container
            ->registerForAutoconfiguration(ScheduledTask::class)
            ->addTag('heypanel.scheduled.task');

        $container
            ->registerForAutoconfiguration(TemplateNamespaceHierarchyBuilderInterface::class)
            ->addTag('heypanel.twig.hierarchy_builder');

        $container
            ->registerForAutoconfiguration(EntityIndexer::class)
            ->addTag('heypanel.entity_indexer');

        $container
            ->registerForAutoconfiguration(ExceptionHandlerInterface::class)
            ->addTag('heypanel.dal.exception_handler');

        $container
            ->registerForAutoconfiguration(AdapterFactoryInterface::class)
            ->addTag('heypanel.filesystem.factory');

        $container->registerAliasForArgument('heypanel.filesystem.private', FilesystemOperator::class, 'privateFilesystem');
        $container->registerAliasForArgument('heypanel.filesystem.public', FilesystemOperator::class, 'publicFilesystem');
    }
}
