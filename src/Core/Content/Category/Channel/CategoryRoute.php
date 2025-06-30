<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category\Channel;

use HeyPanel\Core\Content\Category\Aggregate\CategoryTranslation\CategoryTranslationEntity;
use HeyPanel\Core\Content\Category\CategoryDefinition;
use HeyPanel\Core\Content\Category\CategoryEntity;
use HeyPanel\Core\Content\Category\CategoryException;
use HeyPanel\Core\Content\Cms\Channel\ChannelCmsPageLoaderInterface;
use HeyPanel\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use HeyPanel\Core\Framework\Adapter\Cache\Event\AddCacheTagEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Entity\ChannelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class CategoryRoute extends AbstractCategoryRoute
{
    final public const HOME = 'home';

    /**
     * @internal
     */
    public function __construct(
        private readonly ChannelRepository $categoryRepository,
        private readonly ChannelCmsPageLoaderInterface $cmsPageLoader,
        private readonly CategoryDefinition $categoryDefinition,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public static function buildName(string $id): string
    {
        return 'category-route-' . $id;
    }

    public function getDecorated(): AbstractCategoryRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/client-api/category/{navigationId}', name: 'client-api.category.detail', methods: ['GET', 'POST'])]
    public function load(string $navigationId, Request $request, ChannelContext $context): CategoryRouteResponse
    {
        $this->dispatcher->dispatch(new AddCacheTagEvent(self::buildName($navigationId)));

        if ($navigationId === self::HOME) {
            $navigationId = $context->getChannel()->getNavigationCategoryId();
            $request->attributes->set('navigationId', $navigationId);

            $routeParams = $request->attributes->get('_route_params', []);
            $routeParams['navigationId'] = $navigationId;
            $request->attributes->set('_route_params', $routeParams);
        }

        $category = $this->loadCategory($navigationId, $context);

        $categoryHasContentlessPageType = \in_array($category->getType(), [CategoryDefinition::TYPE_FOLDER, CategoryDefinition::TYPE_LINK], true);
        if ($categoryHasContentlessPageType && $context->getChannel()->getNavigationCategoryId() !== $navigationId) {
            if ($category->getType() === CategoryDefinition::TYPE_LINK) {
                return new CategoryRouteResponse($category);
            }

            throw CategoryException::categoryNotFound($navigationId);
        }

        $pageId = $category->getCmsPageId();
        $channel = $context->getChannel();

        if ($category->getId() === $channel->getNavigationCategoryId() && $channel->getHomeCmsPageId()) {
            $pageId = $channel->getHomeCmsPageId();
            $slotConfig = $channel->getTranslation('homeSlotConfig');
        } else {
            $slotConfig = $this->buildMergedCmsSlotConfig($category, $context);
        }

        if (!$pageId) {
            return new CategoryRouteResponse($category);
        }

        $resolverContext = new EntityResolverContext($context, $request, $this->categoryDefinition, $category);

        $pages = $this->cmsPageLoader->load(
            $request,
            $this->createCriteria($pageId, $request),
            $context,
            $slotConfig,
            $resolverContext,
        );

        $cmsPage = $pages->first();
        if ($cmsPage === null) {
            throw CategoryException::pageNotFound($pageId);
        }

        $category->setCmsPage($cmsPage);
        $category->setCmsPageId($pageId);

        return new CategoryRouteResponse($category);
    }

    private function loadCategory(string $categoryId, ChannelContext $context): CategoryEntity
    {
        $criteria = new Criteria([$categoryId]);
        $criteria->setTitle('category::data');

        $criteria->addAssociation('media');
        $criteria->addAssociation('translations');

        $category = $this->categoryRepository
            ->search($criteria, $context)
            ->get($categoryId);

        if (!$category instanceof CategoryEntity) {
            throw CategoryException::categoryNotFound($categoryId);
        }

        return $category;
    }

    private function createCriteria(string $pageId, Request $request): Criteria
    {
        $criteria = new Criteria([$pageId]);
        $criteria->setTitle('category::cms-page');

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

    /**
     * @return array<string, array<string, mixed>>|null
     */
    private function buildMergedCmsSlotConfig(CategoryEntity $category, ChannelContext $context): ?array
    {
        $inheritanceChain = $context->getLanguageIdChain();
        if (\count($inheritanceChain) <= 1) {
            return $category->getTranslation('slotConfig');
        }

        /** @var non-empty-list<string> $languageMergeOrder */
        $languageMergeOrder = \array_reverse(\array_unique($inheritanceChain));
        $translatedSlotConfigs = $this->getTranslatedSlotConfigs($category, $languageMergeOrder);

        return \array_merge(...$translatedSlotConfigs);
    }

    /**
     * @param non-empty-list<string> $languageMergeOrder
     *
     * @return non-empty-list<array<string, array<string, mixed>>>
     */
    private function getTranslatedSlotConfigs(CategoryEntity $category, array $languageMergeOrder): array
    {
        $getCategoryTranslationByLanguageId = static function (CategoryEntity $category, string $languageId): ?CategoryTranslationEntity {
            return \array_find(
                $category->getTranslations()?->getElements() ?? [],
                static fn (CategoryTranslationEntity $translation) => $translation->getLanguageId() === $languageId,
            );
        };

        return \array_map(static function (string $languageId) use ($category, $getCategoryTranslationByLanguageId) {
            $currentTranslation = $getCategoryTranslationByLanguageId($category, $languageId);

            return $currentTranslation?->getSlotConfig() ?? [];
        }, $languageMergeOrder);
    }
}
