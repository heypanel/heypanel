<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\Channel;

use HeyPanel\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class SeoUrlRoute extends AbstractSeoUrlRoute
{
    /**
     * @internal
     *
     * @param ChannelRepository<SeoUrlCollection> $channelRepository
     */
    public function __construct(private readonly ChannelRepository $channelRepository)
    {
    }

    public function getDecorated(): AbstractSeoUrlRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/client-api/seo-url', name: 'client-api.seo.url', methods: ['GET', 'POST'], defaults: ['_entity' => 'seo_url'])]
    public function load(Request $request, ChannelContext $context, Criteria $criteria): SeoUrlRouteResponse
    {
        return new SeoUrlRouteResponse($this->channelRepository->search($criteria, $context));
    }
}
