<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\MessageQueue\Stats;

use HeyPanel\Core\Framework\Adapter\Messenger\Stamp\SentAtStamp;
use HeyPanel\Core\Framework\MessageQueue\Stats\Entity\MessageStatsResponseEntity;
use Symfony\Component\Messenger\Envelope;

/**
 * @internal
 */
class StatsService
{
    public function __construct(
        private readonly AbstractStatsRepository $statsRepository,
        private readonly bool $enabled,
    ) {
    }

    public function getStats(): MessageStatsResponseEntity
    {
        if (!$this->enabled) {
            return new MessageStatsResponseEntity(enabled: false);
        }

        return new MessageStatsResponseEntity(
            enabled: true,
            stats: $this->statsRepository->getStats()
        );
    }

    public function registerMessage(Envelope $envelope): void
    {
        if (!$this->enabled) {
            return;
        }

        $sentAtStamp = $envelope->last(SentAtStamp::class);
        if ($sentAtStamp === null) {
            return;
        }

        $timeInQueue = time() - $sentAtStamp->getSentAt()->getTimestamp();
        $messageFqcn = \get_class($envelope->getMessage());
        $this->statsRepository->updateMessageStats($messageFqcn, $timeInQueue);
    }
}
