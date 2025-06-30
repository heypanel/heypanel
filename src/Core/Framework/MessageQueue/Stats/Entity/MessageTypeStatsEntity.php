<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\MessageQueue\Stats\Entity;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @internal
 */
class MessageTypeStatsEntity extends Struct
{
    public function __construct(
        public readonly string $type,
        public readonly int $count,
    ) {
    }
}
