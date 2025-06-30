<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Channel;

use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<ChannelContext>
 */
class ContextLoadRouteResponse extends ClientApiResponse
{
    public function getContext(): ChannelContext
    {
        return $this->object;
    }
}
