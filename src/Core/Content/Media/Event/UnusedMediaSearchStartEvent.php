<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Event;

/**
 * @internal
 */
class UnusedMediaSearchStartEvent
{
    public function __construct(public int $totalMedia, public int $totalMediaDeletionCandidates)
    {
    }
}
