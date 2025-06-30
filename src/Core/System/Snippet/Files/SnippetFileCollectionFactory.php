<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Snippet\Files;

class SnippetFileCollectionFactory
{
    /**
     * @internal
     */
    public function __construct(private readonly SnippetFileLoaderInterface $snippetFileLoader)
    {
    }

    public function createSnippetFileCollection(): SnippetFileCollection
    {
        $collection = new SnippetFileCollection();
        $this->snippetFileLoader->loadSnippetFilesIntoCollection($collection);

        return $collection;
    }
}
