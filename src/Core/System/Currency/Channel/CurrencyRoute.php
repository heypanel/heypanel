<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Currency\Channel;

use HeyPanel\Core\Framework\Adapter\Cache\Event\AddCacheTagEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelRepository;
use HeyPanel\Core\System\Currency\CurrencyCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class CurrencyRoute extends AbstractCurrencyRoute
{
    final public const ALL_TAG = 'currency-route';

    /**
     * @internal
     */
    public function __construct(
        private readonly ChannelRepository $currencyRepository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function getDecorated(): AbstractCurrencyRoute
    {
        throw new DecorationPatternException(self::class);
    }

    public static function buildName(string $channelId): string
    {
        return 'currency-route-' . $channelId;
    }

    #[Route(path: '/client-api/currency', name: 'client-api.currency', methods: ['GET', 'POST'], defaults: ['_entity' => 'currency'])]
    public function load(Request $request, ChannelContext $context, Criteria $criteria): CurrencyRouteResponse
    {
        $this->dispatcher->dispatch(new AddCacheTagEvent(
            self::buildName($context->getChannelId()),
            self::ALL_TAG
        ));

        /** @var CurrencyCollection $currencyCollection */
        $currencyCollection = $this->currencyRepository->search($criteria, $context)->getEntities();

        return new CurrencyRouteResponse($currencyCollection);
    }
}
