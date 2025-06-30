<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

use HeyPanel\Core\System\Channel\ChannelContext;

interface HeyPanelChannelEvent extends HeyPanelEvent
{
    public function getChannelContext(): ChannelContext;
}
