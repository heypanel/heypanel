<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\Message;

use HeyPanel\Core\Framework\Feature;
use HeyPanel\Frontend\Theme\AbstractThemePathBuilder;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 *
 * @deprecated tag:v6.8.0 - Will be removed. Unused theme files are now deleted with a scheduled task.
 * @see \HeyPanel\Frontend\Theme\ScheduledTask\DeleteThemeFilesTask
 * @see \HeyPanel\Frontend\Theme\ScheduledTask\DeleteThemeFilesTaskHandler
 */
#[AsMessageHandler]
final class DeleteThemeFilesHandler
{
    public function __construct(
        private readonly FilesystemOperator $filesystem,
        private readonly AbstractThemePathBuilder $pathBuilder,
    ) {
    }

    public function __invoke(DeleteThemeFilesMessage $message): void
    {
        Feature::triggerDeprecationOrThrow(
            'v6.8.0.0',
            Feature::deprecatedMethodMessage(__CLASS__, __METHOD__, 'v6.8.0.0')
        );

        $currentPath = $this->pathBuilder->assemblePath($message->getChannelId(), $message->getThemeId());
        if ($currentPath === $message->getThemePath()) {
            return;
        }

        $this->filesystem->deleteDirectory('theme' . \DIRECTORY_SEPARATOR . $message->getThemePath());
    }
}
