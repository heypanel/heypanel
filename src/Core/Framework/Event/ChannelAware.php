<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

interface ChannelAware
{
    public function getChannelId(): string;
}
