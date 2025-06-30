<?php

declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo;

use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use HeyPanel\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\System\Channel\ChannelEntity;

class ConfiguredSeoUrlRoute implements SeoUrlRouteInterface
{
    public function __construct(
        private readonly SeoUrlRouteInterface $decorated,
        private readonly SeoUrlRouteConfig $config
    ) {
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return $this->config;
    }

    public function prepareCriteria(Criteria $criteria, ChannelEntity $channel): void
    {
        $this->decorated->prepareCriteria($criteria, $channel);
    }

    public function getMapping(Entity $entity, ?ChannelEntity $channel): SeoUrlMapping
    {
        return $this->decorated->getMapping($entity, $channel);
    }
}
