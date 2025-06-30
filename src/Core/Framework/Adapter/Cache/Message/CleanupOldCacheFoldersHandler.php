<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter\Cache\Message;

use HeyPanel\Core\Framework\Adapter\Cache\CacheClearer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler]
final class CleanupOldCacheFoldersHandler
{
    public function __construct(private readonly CacheClearer $cacheClearer)
    {
    }

    public function __invoke(CleanupOldCacheFolders $message): void
    {
        $this->cacheClearer->cleanupOldContainerCacheDirectories();
    }
}
