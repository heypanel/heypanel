<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class ChannelRepositoryNotFoundException extends HeyPanelHttpException
{
    public function __construct(string $entity)
    {
        parent::__construct(
            'ChannelRepository for entity "{{ entityName }}" does not exist.',
            ['entityName' => $entity]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__CHANNEL_REPOSITORY_NOT_FOUND';
    }
}
