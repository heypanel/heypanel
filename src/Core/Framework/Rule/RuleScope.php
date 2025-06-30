<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Rule;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\System\Channel\ChannelContext;

abstract class RuleScope
{
    abstract public function getContext(): Context;

    abstract public function getChannelContext(): ChannelContext;

    public function getCurrentTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
