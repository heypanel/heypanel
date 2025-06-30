<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\DataAbstractionLayer;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Content\Media\Event\MediaIndexerEvent;
use HeyPanel\Core\Content\Media\MediaDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use HeyPanel\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MediaIndexer extends EntityIndexer
{
    /**
     * @internal
     */
    public function __construct(
        private readonly IteratorFactory $iteratorFactory,
        private readonly EntityRepository $repository,
        private readonly EntityRepository $thumbnailRepository,
        private readonly Connection $connection,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly bool $remoteThumbnailsEnable = false
    ) {
    }

    public function getName(): string
    {
        return 'media.indexer';
    }

    public function iterate(?array $offset): ?EntityIndexingMessage
    {
        $iterator = $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);

        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new MediaIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $updates = $event->getPrimaryKeys(MediaDefinition::ENTITY_NAME);

        if (empty($updates)) {
            return null;
        }

        return new MediaIndexingMessage(array_values($updates), null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        if ($this->remoteThumbnailsEnable) {
            return;
        }

        $ids = $message->getData();
        if (!\is_array($ids)) {
            return;
        }

        $ids = array_unique(array_filter($ids));
        if (empty($ids)) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('mediaId', $ids));

        $context = $message->getContext();

        $query = new RetryableQuery(
            $this->connection,
            $this->connection->prepare('UPDATE `media` SET thumbnails_ro = :thumbnails_ro WHERE id = :id')
        );

        $all = $this->thumbnailRepository
            ->search($criteria, $context)
            ->getEntities();

        foreach ($ids as $id) {
            $thumbnails = $all->filterByProperty('mediaId', $id);

            $query->execute([
                'thumbnails_ro' => serialize($thumbnails),
                'id' => Uuid::fromHexToBytes($id),
            ]);
        }

        $this->eventDispatcher->dispatch(new MediaIndexerEvent($ids, $context, $message->getSkip()));
    }

    public function getTotal(): int
    {
        return $this->iteratorFactory->createIterator($this->repository->getDefinition())->fetchCount();
    }

    public function getDecorated(): EntityIndexer
    {
        throw new DecorationPatternException(static::class);
    }
}
