<?php

declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Cms;

use HeyPanel\Core\Content\Category\Service\NavigationLoaderInterface;
use HeyPanel\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use HeyPanel\Core\Content\Cms\DataResolver\CriteriaCollection;
use HeyPanel\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use HeyPanel\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use HeyPanel\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;

class CategoryNavigationCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * @internal
     */
    public function __construct(
        private readonly NavigationLoaderInterface $navigationLoader,
    ) {
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return 'category-navigation';
    }

    /**
     * @codeCoverageIgnore
     */
    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $channelContext = $resolverContext->getChannelContext();
        $channel = $channelContext->getChannel();

        $rootNavigationId = $channel->getNavigationCategoryId();
        $navigationId = $resolverContext->getRequest()->get('navigationId', $rootNavigationId);

        $tree = $this->navigationLoader->load(
            $navigationId,
            $channelContext,
            $rootNavigationId,
            $channel->getNavigationCategoryDepth()
        );

        $slot->setData($tree);
    }
}
