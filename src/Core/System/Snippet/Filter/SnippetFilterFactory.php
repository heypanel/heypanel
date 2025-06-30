<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Snippet\Filter;

use HeyPanel\Core\System\Snippet\SnippetException;

class SnippetFilterFactory
{
    /**
     * @internal
     *
     * @param iterable<SnippetFilterInterface> $filters
     */
    public function __construct(private readonly iterable $filters)
    {
    }

    public function getFilter(string $name): SnippetFilterInterface
    {
        foreach ($this->filters as $filter) {
            if ($filter->supports($name)) {
                return $filter;
            }
        }

        throw SnippetException::filterNotFound($name, self::class);
    }
}
