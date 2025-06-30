<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\DataAbstractionLayer;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Content\Media\Aggregate\MediaFolderConfiguration\MediaFolderConfigurationCollection;
use HeyPanel\Core\Content\Media\Aggregate\MediaFolderConfiguration\MediaFolderConfigurationDefinition;
use HeyPanel\Core\Content\Media\Event\MediaFolderConfigurationIndexerEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use HeyPanel\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MediaFolderConfigurationIndexer extends EntityIndexer
{
    /**
     * @internal
     *
     * @param EntityRepository<MediaFolderConfigurationCollection> $repository
     */
    public function __construct(
        private readonly IteratorFactory $iteratorFactory,
        private readonly EntityRepository $repository,
        private readonly Connection $connection,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getName(): string
    {
        return 'media_folder_configuration.indexer';
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
        $updates = $event->getPrimaryKeys(MediaFolderConfigurationDefinition::ENTITY_NAME);

        if (empty($updates)) {
            return null;
        }

        return new MediaIndexingMessage(array_values($updates), null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();
        if (!\is_array($ids)) {
            return;
        }

        $ids = array_unique(array_filter($ids));
        if (empty($ids)) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addAssociation('mediaThumbnailSizes');
        $criteria->setIds($ids);

        $context = $message->getContext();

        $configs = $this->repository->search($criteria, $context)->getEntities();

        $update = new RetryableQuery(
            $this->connection,
            $this->connection->prepare('UPDATE media_folder_configuration SET media_thumbnail_sizes_ro = :media_thumbnail_sizes_ro WHERE id = :id')
        );

        foreach ($configs as $config) {
            $update->execute([
                'media_thumbnail_sizes_ro' => serialize($config->getMediaThumbnailSizes()),
                'id' => Uuid::fromHexToBytes($config->getId()),
            ]);
        }

        $this->eventDispatcher->dispatch(new MediaFolderConfigurationIndexerEvent($ids, $context, $message->getSkip()));
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
