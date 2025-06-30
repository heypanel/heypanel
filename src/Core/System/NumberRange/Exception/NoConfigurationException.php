<?php declare(strict_types=1);

namespace HeyPanel\Core\System\NumberRange\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class NoConfigurationException extends HeyPanelHttpException
{
    public function __construct(
        string $entityName,
        ?string $channelId = null
    ) {
        parent::__construct(
            'No number range configuration found for entity "{{ entity }}" with channel "{{ channelId }}".',
            ['entity' => $entityName, 'channelId' => $channelId]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__NO_NUMBER_RANGE_CONFIGURATION';
    }
}
