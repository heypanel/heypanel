<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\LandingPage\Channel;

use HeyPanel\Core\Content\LandingPage\LandingPageEntity;
use HeyPanel\Core\System\Channel\ClientApiResponse;

/**
 * @extends ClientApiResponse<LandingPageEntity>
 */
class LandingPageRouteResponse extends ClientApiResponse
{
    public function getLandingPage(): LandingPageEntity
    {
        return $this->object;
    }
}
