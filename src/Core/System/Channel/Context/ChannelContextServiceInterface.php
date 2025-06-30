<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Context;

use HeyPanel\Core\System\Channel\ChannelContext;

/**
 * @internal
 */
interface ChannelContextServiceInterface
{
    public function get(ChannelContextServiceParameters $parameters): ChannelContext;
}
