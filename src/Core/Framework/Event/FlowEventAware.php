<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

use HeyPanel\Core\Framework\Event\EventData\EventDataCollection;

interface FlowEventAware extends HeyPanelEvent
{
    public static function getAvailableData(): EventDataCollection;

    public function getName(): string;
}
