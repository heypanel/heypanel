<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\MessageQueue\Stats\Entity;

use HeyPanel\Core\Framework\Struct\Collection;

/**
 * @internal
 *
 * @extends Collection<MessageTypeStatsEntity>
 */
class MessageTypeStatsCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return MessageTypeStatsEntity::class;
    }
}
