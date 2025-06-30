<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Context;

use HeyPanel\Core\System\Channel\BaseChannelContext;

abstract class AbstractBaseChannelContextFactory
{
    /**
     * @param array<string, mixed> $options
     */
    abstract public function create(string $channelId, array $options = []): BaseChannelContext;
}
