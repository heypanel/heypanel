<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
class StoreLicenseSubscriptionStruct extends Struct
{
    protected \DateTimeInterface $expirationDate;

    public function getApiAlias(): string
    {
        return 'store_license_subscription';
    }
}
