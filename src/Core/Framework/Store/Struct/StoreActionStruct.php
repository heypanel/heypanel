<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Struct;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @codeCoverageIgnore
 */
class StoreActionStruct extends Struct
{
    protected string $label;

    protected string $externalLink;

    public function getApiAlias(): string
    {
        return 'store_action';
    }
}
