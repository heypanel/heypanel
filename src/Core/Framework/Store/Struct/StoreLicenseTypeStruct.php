<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
class StoreLicenseTypeStruct extends Struct
{
    protected string $name;

    public function getApiAlias(): string
    {
        return 'store_license_type';
    }
}
