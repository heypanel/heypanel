<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\SeoUrlRoute;

use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\System\Channel\ChannelEntity;

interface SeoUrlRouteInterface
{
    public function getConfig(): SeoUrlRouteConfig;

    public function prepareCriteria(Criteria $criteria, ChannelEntity $channel): void;

    public function getMapping(Entity $entity, ?ChannelEntity $channel): SeoUrlMapping;
}
