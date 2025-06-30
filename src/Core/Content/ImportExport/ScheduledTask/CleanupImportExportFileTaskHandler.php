<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\ScheduledTask;

use HeyPanel\Core\Content\ImportExport\Service\DeleteExpiredFilesService;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler(handles: CleanupImportExportFileTask::class)]
final class CleanupImportExportFileTaskHandler extends ScheduledTaskHandler
{
    /**
     * @internal
     */
    public function __construct(
        EntityRepository $repository,
        LoggerInterface $logger,
        private readonly DeleteExpiredFilesService $deleteExpiredFilesService
    ) {
        parent::__construct($repository, $logger);
    }

    public function run(): void
    {
        $this->deleteExpiredFilesService->deleteFiles(Context::createCLIContext());
    }
}
