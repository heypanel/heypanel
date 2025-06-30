<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Provider;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use HeyPanel\Core\Content\Category\CategoryDefinition;
use HeyPanel\Core\Content\Category\CategoryEntity;
use HeyPanel\Core\Content\Category\Event\ChannelCategoryIdsFetchedEvent;
use HeyPanel\Core\Content\Sitemap\Service\ConfigHandler;
use HeyPanel\Core\Content\Sitemap\Struct\Url;
use HeyPanel\Core\Content\Sitemap\Struct\UrlResult;
use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use HeyPanel\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

class CategoryUrlProvider extends AbstractUrlProvider
{
    final public const CHANGE_FREQ = 'daily';

    /**
     * @internal
     */
    public function __construct(
        private readonly ConfigHandler $configHandler,
        private readonly Connection $connection,
        private readonly CategoryDefinition $definition,
        private readonly IteratorFactory $iteratorFactory,
        private readonly RouterInterface $router,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getDecorated(): AbstractUrlProvider
    {
        throw new DecorationPatternException(self::class);
    }

    public function getName(): string
    {
        return 'category';
    }

    public function getUrls(ChannelContext $context, int $limit, ?int $offset = null): UrlResult
    {
        $categories = $this->getCategories($context, $limit, $offset);

        if (empty($categories)) {
            return new UrlResult([], null);
        }

        $keys = FetchModeHelper::keyPair($categories);
        $autoIncrementIds = array_keys($keys);

        // The next offset must be taken from all results before the event can filter any ids out to prevent fetching
        // the same ids again
        $nextOffset = array_pop($autoIncrementIds);
        \assert(\is_int($nextOffset) || $nextOffset === null);

        $categoryIdsFetchedEvent = $this->eventDispatcher->dispatch(
            new ChannelCategoryIdsFetchedEvent(\array_column($categories, 'id'), $context)
        );

        if (empty($categoryIdsFetchedEvent->getIds())) {
            return new UrlResult([], $nextOffset);
        }

        $availableCategories = \array_filter(
            $categories,
            fn (array $category) => $categoryIdsFetchedEvent->hasId($category['id'])
        );

        $seoUrls = $this->getSeoUrls($categoryIdsFetchedEvent->getIds(), 'frontend.navigation.page', $context, $this->connection);

        /** @var array<string, array{seo_path_info: string}> $seoUrls */
        $seoUrls = FetchModeHelper::groupUnique($seoUrls);

        $urls = [];
        $url = new Url();

        foreach ($availableCategories as $category) {
            $lastMod = $category['updated_at'] ?: $category['created_at'];

            $lastMod = (new \DateTime($lastMod))->format(Defaults::STORAGE_DATE_TIME_FORMAT);

            $newUrl = clone $url;

            if (isset($seoUrls[$category['id']])) {
                $newUrl->setLoc($seoUrls[$category['id']]['seo_path_info']);
            } else {
                $newUrl->setLoc($this->router->generate('frontend.navigation.page', ['navigationId' => $category['id']]));
            }

            $newUrl->setLastmod(new \DateTime($lastMod));
            $newUrl->setChangefreq(self::CHANGE_FREQ);
            $newUrl->setResource(CategoryEntity::class);
            $newUrl->setIdentifier($category['id']);

            $urls[] = $newUrl;
        }

        return new UrlResult($urls, $nextOffset);
    }

    /**
     * @return list<array{id: string, created_at: string, updated_at: string}>
     */
    private function getCategories(ChannelContext $context, int $limit, ?int $offset): array
    {
        $lastId = null;
        if ($offset) {
            $lastId = ['offset' => $offset];
        }

        $iterator = $this->iteratorFactory->createIterator($this->definition, $lastId);
        $query = $iterator->getQuery();
        $query->setMaxResults($limit);

        $query->addSelect(
            '`category`.created_at',
            '`category`.updated_at',
        );

        $wheres = [];
        $categoryIds = array_filter([
            $context->getChannel()->getNavigationCategoryId(),
            $context->getChannel()->getFooterCategoryId(),
            $context->getChannel()->getServiceCategoryId(),
        ]);

        foreach ($categoryIds as $id) {
            $wheres[] = '`category`.path LIKE ' . $query->createNamedParameter('%|' . $id . '|%');
        }

        $query->andWhere('(' . implode(' OR ', $wheres) . ')');
        $query->andWhere('`category`.version_id = :versionId');
        $query->andWhere('`category`.active = 1');
        $query->andWhere('`category`.type != :linkType');
        $query->andWhere('`category`.type != :folderType');

        $excludedCategoryIds = $this->getExcludedCategoryIds($context);
        if (!empty($excludedCategoryIds)) {
            $query->andWhere('`category`.id NOT IN (:categoryIds)');
            $query->setParameter('categoryIds', Uuid::fromHexToBytesList($excludedCategoryIds), ArrayParameterType::BINARY);
        }

        $query->setParameter('versionId', Uuid::fromHexToBytes(Defaults::LIVE_VERSION));
        $query->setParameter('linkType', CategoryDefinition::TYPE_LINK);
        $query->setParameter('folderType', CategoryDefinition::TYPE_FOLDER);

        /** @var list<array{id: string, created_at: string, updated_at: string}> $result */
        $result = $query->executeQuery()->fetchAllAssociative();

        return $result;
    }

    /**
     * @return array<string>
     */
    private function getExcludedCategoryIds(ChannelContext $channelContext): array
    {
        $channelId = $channelContext->getChannelId();

        $excludedUrls = $this->configHandler->get(ConfigHandler::EXCLUDED_URLS_KEY);
        if (empty($excludedUrls)) {
            return [];
        }

        $excludedUrls = array_filter($excludedUrls, static function (array $excludedUrl) use ($channelId) {
            if ($excludedUrl['resource'] !== CategoryEntity::class) {
                return false;
            }

            if ($excludedUrl['channelId'] !== $channelId) {
                return false;
            }

            return true;
        });

        return array_column($excludedUrls, 'identifier');
    }
}
