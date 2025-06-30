<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\Channel;

use HeyPanel\Core\Content\Category\CategoryEntity;
use HeyPanel\Core\Content\Product\Channel\ChannelProductEntity;
use HeyPanel\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use HeyPanel\Core\Content\Seo\SeoUrl\SeoUrlEntity;
use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface as SeoUrlRouteConfigRoute;
use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use HeyPanel\Core\Framework\Struct\Collection;
use HeyPanel\Core\Framework\Struct\Struct;
use HeyPanel\Core\PlatformRequest;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\ClientApiResponse;
use HeyPanel\Core\System\Channel\Entity\ChannelDefinitionInstanceRegistry;
use HeyPanel\Core\System\Channel\Entity\ChannelRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 */
class StoreApiSeoResolver implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly ChannelRepository $channelRepository,
        private readonly DefinitionInstanceRegistry $definitionInstanceRegistry,
        private readonly ChannelDefinitionInstanceRegistry $channelDefinitionInstanceRegistry,
        private readonly SeoUrlRouteRegistry $seoUrlRouteRegistry
    ) {
    }

    /**
     * This subscriber has to trigger before the {@see \HeyPanel\Core\System\Channel\Api\StoreApiResponseListener},
     * because it requires access to the `ClientApiResponse`'s struct object, which is not available after encoding it.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['addSeoInformation', 11000],
        ];
    }

    public function addSeoInformation(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response instanceof ClientApiResponse) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->headers->has(PlatformRequest::HEADER_INCLUDE_SEO_URLS)) {
            return;
        }

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CHANNEL_CONTEXT_OBJECT);

        if (!$context instanceof ChannelContext) {
            // This is likely the case for routes with the `auth_required` option set to `false`,
            // where the channel-id and context is not resolved by access-token by the other listeners.
            return;
        }

        $dataBag = new SeoResolverData();

        $this->find($dataBag, $response->getObject());
        $this->enrich($dataBag, $context);
    }

    private function find(SeoResolverData $data, Struct $struct): void
    {
        if ($struct instanceof AggregationResultCollection) {
            foreach ($struct as $item) {
                $this->findStruct($data, $item);
            }
        }

        if ($struct instanceof EntitySearchResult) {
            foreach ($struct->getEntities() as $entity) {
                $this->findStruct($data, $entity);
            }

            foreach ($struct->getExtensions() as $extension) {
                $this->findStruct($data, $extension);
            }
        }

        if ($struct instanceof Collection) {
            foreach ($struct as $item) {
                $this->findStruct($data, $item);
            }
        }

        $this->findStruct($data, $struct);
    }

    private function findStruct(SeoResolverData $data, Struct $struct): void
    {
        if ($struct instanceof Entity) {
            $definition = $this->definitionInstanceRegistry->getByEntityClass($struct) ?? $this->channelDefinitionInstanceRegistry->getByEntityClass($struct);
            if ($definition && $definition->isSeoAware()) {
                $data->add($definition->getEntityName(), $struct);
            }
        }

        foreach ($struct->getVars() as $item) {
            if ($item instanceof Collection || \is_array($item)) {
                foreach ($item as $collectionItem) {
                    if ($collectionItem instanceof Struct) {
                        $this->findStruct($data, $collectionItem);
                    }
                }
            } elseif ($item instanceof Struct) {
                $this->findStruct($data, $item);
            }
        }
    }

    private function enrich(SeoResolverData $data, ChannelContext $context): void
    {
        foreach ($data->getEntities() as $definition) {
            $definition = (string) $definition;

            $ids = $data->getIds($definition);
            $routes = $this->seoUrlRouteRegistry->findByDefinition($definition);
            if (\count($routes) === 0) {
                continue;
            }

            $routes = array_map(static fn (SeoUrlRouteConfigRoute $seoUrlRoute) => $seoUrlRoute->getConfig()->getRouteName(), $routes);

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('isCanonical', true));
            $criteria->addFilter(new EqualsAnyFilter('routeName', $routes));
            $criteria->addFilter(new EqualsAnyFilter('foreignKey', $ids));
            $criteria->addFilter(new EqualsFilter('languageId', $context->getLanguageId()));
            $criteria->addSorting(new FieldSorting('channelId'));

            /** @var SeoUrlEntity $url */
            foreach ($this->channelRepository->search($criteria, $context) as $url) {
                /** @var ChannelProductEntity|CategoryEntity $entity */
                $entity = $data->get($definition, $url->getForeignKey());

                if ($entity->getSeoUrls() === null) {
                    $entity->setSeoUrls(new SeoUrlCollection());
                }

                /** @var SeoUrlCollection $seoUrlCollection */
                $seoUrlCollection = $entity->getSeoUrls();
                $seoUrlCollection->add($url);
            }
        }
    }
}
