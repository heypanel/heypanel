<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\LandingPage\Channel;

use HeyPanel\Core\Content\Cms\Channel\ChannelCmsPageLoaderInterface;
use HeyPanel\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use HeyPanel\Core\Content\LandingPage\LandingPageDefinition;
use HeyPanel\Core\Content\LandingPage\LandingPageEntity;
use HeyPanel\Core\Content\LandingPage\LandingPageException;
use HeyPanel\Core\Framework\Adapter\Cache\Event\AddCacheTagEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class LandingPageRoute extends AbstractLandingPageRoute
{
    /**
     * @internal
     */
    public function __construct(
        private readonly ChannelRepository $landingPageRepository,
        private readonly ChannelCmsPageLoaderInterface $cmsPageLoader,
        private readonly LandingPageDefinition $landingPageDefinition,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public static function buildName(string $id): string
    {
        return 'landing-page-route-' . $id;
    }

    public function getDecorated(): AbstractLandingPageRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/client-api/landing-page/{landingPageId}', name: 'client-api.landing-page.detail', methods: ['POST'])]
    public function load(string $landingPageId, Request $request, ChannelContext $context): LandingPageRouteResponse
    {
        $this->dispatcher->dispatch(new AddCacheTagEvent(self::buildName($landingPageId)));

        $landingPage = $this->loadLandingPage($landingPageId, $context);

        $pageId = $landingPage->getCmsPageId();

        if (!$pageId) {
            return new LandingPageRouteResponse($landingPage);
        }

        $resolverContext = new EntityResolverContext($context, $request, $this->landingPageDefinition, $landingPage);

        $pages = $this->cmsPageLoader->load(
            $request,
            $this->createCriteria($pageId, $request),
            $context,
            $landingPage->getTranslation('slotConfig'),
            $resolverContext
        );

        $cmsPage = $pages->first();
        if ($cmsPage === null) {
            throw LandingPageException::notFound($pageId);
        }

        $landingPage->setCmsPage($cmsPage);

        return new LandingPageRouteResponse($landingPage);
    }

    private function loadLandingPage(string $landingPageId, ChannelContext $context): LandingPageEntity
    {
        $criteria = new Criteria([$landingPageId]);
        $criteria->setTitle('landing-page::data');

        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('channels.id', $context->getChannelId()));

        $landingPage = $this->landingPageRepository
            ->search($criteria, $context)
            ->get($landingPageId);

        if (!$landingPage instanceof LandingPageEntity) {
            throw LandingPageException::notFound($landingPageId);
        }

        return $landingPage;
    }

    private function createCriteria(string $pageId, Request $request): Criteria
    {
        $criteria = new Criteria([$pageId]);
        $criteria->setTitle('landing-page::cms-page');

        $slots = $request->get('slots');

        if (\is_string($slots)) {
            $slots = explode('|', $slots);
        }

        if (!empty($slots) && \is_array($slots)) {
            $criteria
                ->getAssociation('sections.blocks')
                ->addFilter(new EqualsAnyFilter('slots.id', $slots));
        }

        return $criteria;
    }
}
