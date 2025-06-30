<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
class AccessTokenStruct extends Struct
{
    public function __construct(
        protected ShopUserTokenStruct $shopUserToken,
        protected ?string $shopSecret = null,
    ) {
    }

    public function getShopUserToken(): ShopUserTokenStruct
    {
        return $this->shopUserToken;
    }

    public function getShopSecret(): ?string
    {
        return $this->shopSecret;
    }

    public function getApiAlias(): string
    {
        return 'store_access_token';
    }
}
