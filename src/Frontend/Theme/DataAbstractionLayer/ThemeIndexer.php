<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\DataAbstractionLayer;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use HeyPanel\Core\Framework\DataAbstractionLayer\Doctrine\RetryableTransaction;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use HeyPanel\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Frontend\Theme\Event\ThemeIndexerEvent;
use HeyPanel\Frontend\Theme\ThemeCollection;
use HeyPanel\Frontend\Theme\ThemeDefinition;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ThemeIndexer extends EntityIndexer
{
    /**
     * @internal
     *
     * @param EntityRepository<ThemeCollection> $repository
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
        return 'theme.indexer';
    }

    public function iterate(?array $offset): ?EntityIndexingMessage
    {
        $iterator = $this->getIterator($offset);

        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new ThemeIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $updates = $event->getPrimaryKeysWithPropertyChange(ThemeDefinition::ENTITY_NAME, ['parentThemeId']);

        if (empty($updates)) {
            return null;
        }

        return new ThemeIndexingMessage(array_values($updates), null, $event->getContext());
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

        $context = $message->getContext();

        RetryableTransaction::retryable($this->connection, function () use ($ids): void {
            $this->connection->executeStatement(
                'DELETE FROM theme_child WHERE parent_id IN (:ids)',
                ['ids' => Uuid::fromHexToBytesList($ids)],
                ['ids' => ArrayParameterType::BINARY]
            );

            $this->connection->executeStatement(
                'INSERT IGNORE INTO theme_child (child_id, parent_id)
                    (
                        SELECT id as child_id, parent_theme_id as parent_id FROM theme
                        WHERE parent_theme_id IS NOT NULL AND id IN (:ids) AND technical_name IS NULL
                    )
                ',
                ['ids' => Uuid::fromHexToBytesList($ids)],
                ['ids' => ArrayParameterType::BINARY]
            );
        });

        $this->eventDispatcher->dispatch(new ThemeIndexerEvent($ids, $context, $message->getSkip()));
    }

    public function getTotal(): int
    {
        return $this->getIterator(null)->fetchCount();
    }

    public function getDecorated(): EntityIndexer
    {
        throw new DecorationPatternException(static::class);
    }

    /**
     * @param array{offset: int|null}|null $offset
     */
    private function getIterator(?array $offset): IterableQuery
    {
        return $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);
    }
}
