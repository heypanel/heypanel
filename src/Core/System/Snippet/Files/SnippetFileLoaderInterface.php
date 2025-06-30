<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Snippet\Files;

interface SnippetFileLoaderInterface
{
    public function loadSnippetFilesIntoCollection(SnippetFileCollection $snippetFileCollection): void;
}
