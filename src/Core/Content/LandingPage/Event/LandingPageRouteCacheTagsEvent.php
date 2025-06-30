<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\LandingPage\Event;

use HeyPanel\Core\Content\LandingPage\Channel\LandingPageRouteResponse;
use HeyPanel\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Feature;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\ClientApiResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated tag:v6.8.0 - Will be removed in 6.8.0 as it was not used anymore
 */
class LandingPageRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
    /**
     * @param array<string|null> $tags
     * @param LandingPageRouteResponse $response
     */
    public function __construct(
        protected string $landingPageId,
        array $tags,
        Request $request,
        ClientApiResponse $response,
        ChannelContext $context,
        ?Criteria $criteria
    ) {
        Feature::triggerDeprecationOrThrow(
            'v6.8.0.0',
            Feature::deprecatedClassMessage(self::class, 'v6.8.0.0'),
        );

        parent::__construct($tags, $request, $response, $context, $criteria);
    }

    public function getLandingPageId(): string
    {
        Feature::triggerDeprecationOrThrow(
            'v6.8.0.0',
            Feature::deprecatedClassMessage(self::class, 'v6.8.0.0'),
        );

        return $this->landingPageId;
    }
}
