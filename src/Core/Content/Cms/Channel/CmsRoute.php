<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\Channel;

use HeyPanel\Core\Content\Cms\Exception\PageNotFoundException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class CmsRoute extends AbstractCmsRoute
{
    /**
     * @internal
     */
    public function __construct(private readonly ChannelCmsPageLoaderInterface $cmsPageLoader)
    {
    }

    public function getDecorated(): AbstractCmsRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/client-api/cms/{id}', name: 'client-api.cms.detail', methods: ['GET', 'POST'])]
    public function load(string $id, Request $request, ChannelContext $context): CmsRouteResponse
    {
        $criteria = new Criteria([$id]);

        $slots = $request->get('slots');

        if (\is_string($slots)) {
            $slots = explode('|', $slots);
        }

        if (!empty($slots)) {
            $criteria
                ->getAssociation('sections.blocks')
                ->addFilter(new EqualsAnyFilter('slots.id', $slots));
        }

        $pages = $this->cmsPageLoader->load($request, $criteria, $context);

        $cmsPage = $pages->first();
        if ($cmsPage === null) {
            throw new PageNotFoundException($id);
        }

        return new CmsRouteResponse($cmsPage);
    }
}
