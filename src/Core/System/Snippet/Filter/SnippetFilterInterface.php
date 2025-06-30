<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Snippet\Filter;

use HeyPanel\Core\System\Snippet\SnippetService;

/**
 * @phpstan-import-type SnippetArray from SnippetService
 */
interface SnippetFilterInterface
{
    public function getName(): string;

    public function supports(string $name): bool;

    /**
     * @param SnippetArray $snippets
     * @param true|string|list<string> $requestFilterValue
     *
     * @return SnippetArray
     */
    public function filter(array $snippets, $requestFilterValue): array;
}
