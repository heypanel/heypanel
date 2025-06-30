<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Language\Channel;

use HeyPanel\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use HeyPanel\Core\System\Channel\ClientApiResponse;
use HeyPanel\Core\System\Language\LanguageCollection;

/**
 * @extends ClientApiResponse<EntitySearchResult<LanguageCollection>>
 */
class LanguageRouteResponse extends ClientApiResponse
{
    public function getLanguages(): LanguageCollection
    {
        return $this->object->getEntities();
    }
}
