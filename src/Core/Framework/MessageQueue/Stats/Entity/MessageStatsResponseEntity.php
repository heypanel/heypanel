<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\MessageQueue\Stats\Entity;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @internal
 */
class MessageStatsResponseEntity extends Struct
{
    public function __construct(
        public readonly bool $enabled,
        public readonly ?MessageStatsEntity $stats = null,
    ) {
    }
}
