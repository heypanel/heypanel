<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Indexing;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\MessageQueue\AsyncMessageInterface;

class EntityIndexingMessage implements AsyncMessageInterface
{
    protected string $indexer;

    private readonly Context $context;

    /**
     * @var array<string>
     */
    private array $skip = [];

    /**
     * @param array<string>|string $data
     * @param array{offset: int|null}|null $offset
     */
    public function __construct(
        protected array|string $data,
        protected ?array $offset = null,
        ?Context $context = null,
        public bool $forceQueue = false,
        public bool $isFullIndexing = false
    ) {
        $this->context = $context ?? Context::createDefaultContext();
    }

    /**
     * @return array<string>|string
     */
    public function getData(): array|string
    {
        return $this->data;
    }

    /**
     * @return array{offset: int|null}|null
     */
    public function getOffset(): ?array
    {
        return $this->offset;
    }

    /**
     * @internal This property is called by the indexer registry. The indexer name is stored in this message to identify the message handler in the queue worker
     */
    public function getIndexer(): string
    {
        return $this->indexer;
    }

    /**
     * @internal This property is called by the indexer registry. The indexer name is stored in this message to identify the message handler in the queue worker
     */
    public function setIndexer(string $indexer): void
    {
        $this->indexer = $indexer;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function forceQueue(): bool
    {
        return $this->forceQueue;
    }

    /**
     * @return array<string>
     */
    public function getSkip(): array
    {
        return $this->skip;
    }

    /**
     * @param array<string> $skip
     */
    public function setSkip(array $skip): void
    {
        $this->skip = \array_unique(\array_values($skip));
    }

    public function addSkip(string ...$skip): void
    {
        $this->skip = \array_unique(\array_merge($this->skip, \array_values($skip)));
    }

    public function allow(string $name): bool
    {
        return !\in_array($name, $this->getSkip(), true);
    }
}
