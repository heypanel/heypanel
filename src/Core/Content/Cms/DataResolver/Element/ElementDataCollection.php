<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Cms\DataResolver\Element;

use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCollection;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

/**
 * @implements \IteratorAggregate<array-key, EntitySearchResult<EntityCollection>>
 */
class ElementDataCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array<string, EntitySearchResult<EntityCollection>>
     */
    protected array $searchResults = [];

    /**
     * @param EntitySearchResult<EntityCollection> $entitySearchResult
     */
    public function add(string $key, EntitySearchResult $entitySearchResult): void
    {
        $this->searchResults[$key] = $entitySearchResult;
    }

    /**
     * @return EntitySearchResult<EntityCollection>|null
     */
    public function get(string $key): ?EntitySearchResult
    {
        return $this->searchResults[$key] ?? null;
    }

    /**
     * @return \Traversable<string, EntitySearchResult<EntityCollection>>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->searchResults;
    }

    public function count(): int
    {
        return \count($this->searchResults);
    }
}
