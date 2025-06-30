<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Context;

use HeyPanel\Core\Framework\Context;

class AdminChannelApiSource extends ChannelApiSource
{
    public string $type = 'admin-channel-api';

    protected Context $originalContext;

    public function __construct(
        string $channelId,
        Context $originalContext
    ) {
        parent::__construct($channelId);

        $this->originalContext = $originalContext;
    }

    public function getOriginalContext(): Context
    {
        return $this->originalContext;
    }
}
