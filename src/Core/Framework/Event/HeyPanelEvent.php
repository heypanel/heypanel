<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

use HeyPanel\Core\Framework\Context;

interface HeyPanelEvent
{
    public function getContext(): Context;
}
