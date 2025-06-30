<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Language\Channel;

use HeyPanel\Core\Framework\Adapter\Cache\Event\AddCacheTagEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelRepository;
use HeyPanel\Core\System\Language\LanguageCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class LanguageRoute extends AbstractLanguageRoute
{
    final public const ALL_TAG = 'language-route';

    /**
     * @internal
     *
     * @param ChannelRepository<LanguageCollection> $repository
     */
    public function __construct(
        private readonly ChannelRepository $repository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public static function buildName(string $id): string
    {
        return 'language-route-' . $id;
    }

    public function getDecorated(): AbstractLanguageRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/client-api/language', name: 'client-api.language', methods: ['GET', 'POST'], defaults: ['_entity' => 'language'])]
    public function load(Request $request, ChannelContext $context, Criteria $criteria): LanguageRouteResponse
    {
        $this->dispatcher->dispatch(new AddCacheTagEvent(
            self::buildName($context->getChannelId()),
            self::ALL_TAG
        ));

        $criteria->addAssociation('translationCode');

        return new LanguageRouteResponse(
            $this->repository->search($criteria, $context)
        );
    }
}
