<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Currency\Channel;

use HeyPanel\Core\System\Channel\ClientApiResponse;
use HeyPanel\Core\System\Currency\CurrencyCollection;

/**
 * @extends ClientApiResponse<CurrencyCollection>
 */
class CurrencyRouteResponse extends ClientApiResponse
{
    public function getCurrencies(): CurrencyCollection
    {
        return $this->object;
    }
}
