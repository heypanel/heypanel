<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use HeyPanel\Core\System\Channel\ChannelContext;

class AlreadyLockedException extends HeyPanelHttpException
{
    public function __construct(ChannelContext $channelContext)
    {
        parent::__construct('Cannot acquire lock for channel {{channelId}} and language {{languageId}}', [
            'channelId' => $channelContext->getChannelId(),
            'languageId' => $channelContext->getLanguageId(),
        ]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__SITEMAP_ALREADY_LOCKED';
    }
}
