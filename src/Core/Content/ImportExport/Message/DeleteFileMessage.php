<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\Message;

use HeyPanel\Core\Framework\MessageQueue\AsyncMessageInterface;

class DeleteFileMessage implements AsyncMessageInterface
{
    private array $files = [];

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(array $files): void
    {
        $this->files = $files;
    }
}
