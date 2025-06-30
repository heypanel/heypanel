<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DependencyInjection\CompilerPass;

use HeyPanel\Core\Framework\DataAbstractionLayer\AttributeEntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\AttributeMappingDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\AttributeTranslationDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use HeyPanel\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use HeyPanel\Core\Framework\DataAbstractionLayer\VersionManager;
use HeyPanel\Core\Framework\DependencyInjection\DependencyInjectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

class EntityCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->collectDefinitions($container);
        $this->makeFieldSerializersPublic($container);
        $this->makeFieldResolversPublic($container);
        $this->makeFieldAccessorBuildersPublic($container);
    }

    private function collectDefinitions(ContainerBuilder $container): void
    {
        $entityNameMap = [];
        $repositoryNameMap = [];
        $services = $container->findTaggedServiceIds('heypanel.entity.definition');

        $ids = array_keys($services);

        foreach ($ids as $serviceId) {
            $service = $container->getDefinition($serviceId);

            $service->addMethodCall('compile', [
                new Reference(DefinitionInstanceRegistry::class),
            ]);
            $service->setPublic(true);

            /** @var string $class */
            $class = $service->getClass();

            if (!\is_subclass_of($class, EntityDefinition::class)) {
                throw DependencyInjectionException::taggedServiceHasWrongType($serviceId, 'heypanel.entity.definition', EntityDefinition::class);
            }

            if (\in_array($class, [AttributeEntityDefinition::class, AttributeTranslationDefinition::class, AttributeMappingDefinition::class], true)) {
                continue;
            }

            $instance = new $class();

            $entityNameMap[$instance->getEntityName()] = $serviceId;
            $entity = $instance->getEntityName();

            $repositoryId = $instance->getEntityName() . '.repository';

            try {
                $repository = $container->getDefinition($repositoryId);
            } catch (ServiceNotFoundException) {
                $repository = new Definition(
                    EntityRepository::class,
                    [
                        new Reference($serviceId),
                        new Reference(EntityReaderInterface::class),
                        new Reference(VersionManager::class),
                        new Reference(EntitySearcherInterface::class),
                        new Reference(EntityAggregatorInterface::class),
                        new Reference('event_dispatcher'),
                        new Reference(EntityLoadedEventFactory::class),
                    ]
                );
                $container->setDefinition($repositoryId, $repository);
            }
            $repository->setPublic(true);
            $container->registerAliasForArgument($repositoryId, EntityRepository::class);
            $container->registerAliasForArgument($repositoryId, EntityRepository::class);

            $repositoryNameMap[$entity] = $repositoryId;
        }

        $definitionRegistry = $container->getDefinition(DefinitionInstanceRegistry::class);
        $definitionRegistry->replaceArgument(1, $entityNameMap);
        $definitionRegistry->replaceArgument(2, $repositoryNameMap);
    }

    private function makeFieldSerializersPublic(ContainerBuilder $container): void
    {
        $servicesIds = array_keys($container->findTaggedServiceIds('heypanel.field_serializer'));

        foreach ($servicesIds as $servicesId) {
            $container->getDefinition($servicesId)->setPublic(true);
        }
    }

    private function makeFieldResolversPublic(ContainerBuilder $container): void
    {
        $servicesIds = array_keys($container->findTaggedServiceIds('heypanel.field_resolver'));

        foreach ($servicesIds as $servicesId) {
            $container->getDefinition($servicesId)->setPublic(true);
        }
    }

    private function makeFieldAccessorBuildersPublic(ContainerBuilder $container): void
    {
        $servicesIds = array_keys($container->findTaggedServiceIds('heypanel.field_accessor_builder'));

        foreach ($servicesIds as $servicesId) {
            $container->getDefinition($servicesId)->setPublic(true);
        }
    }
}
