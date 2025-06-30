<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Channel;

use HeyPanel\Core\Content\Media\MediaCollection;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<MediaCollection>
 */
class MediaRouteResponse extends ClientApiResponse
{
    public function getMediaCollection(): MediaCollection
    {
        return $this->object;
    }
}
