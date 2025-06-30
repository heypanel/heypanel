<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Message;

use HeyPanel\Core\Content\Media\MediaException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\Visibility;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler]
final class DeleteFileHandler
{
    /**
     * @internal
     */
    public function __construct(
        private readonly FilesystemOperator $filesystemPublic,
        private readonly FilesystemOperator $filesystemPrivate
    ) {
    }

    public function __invoke(DeleteFileMessage $message): void
    {
        foreach ($message->getFiles() as $file) {
            try {
                $this->getFileSystem($message->getVisibility())->delete($file);
            } catch (UnableToDeleteFile) {
                // ignore file is already deleted
            }
        }
    }

    private function getFileSystem(string $visibility): FilesystemOperator
    {
        return match ($visibility) {
            Visibility::PUBLIC => $this->filesystemPublic,
            Visibility::PRIVATE => $this->filesystemPrivate,
            default => throw MediaException::invalidFilesystemVisibility(),
        };
    }
}
