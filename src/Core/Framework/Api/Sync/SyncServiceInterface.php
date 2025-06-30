<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Sync;

use Doctrine\DBAL\ConnectionException;
use HeyPanel\Core\Framework\Api\Exception\InvalidSyncOperationException;
use HeyPanel\Core\Framework\Context;

interface SyncServiceInterface
{
    /**
     * @param list<SyncOperation> $operations
     *
     * @throws ConnectionException
     * @throws InvalidSyncOperationException
     */
    public function sync(array $operations, Context $context, SyncBehavior $behavior): SyncResult;
}
