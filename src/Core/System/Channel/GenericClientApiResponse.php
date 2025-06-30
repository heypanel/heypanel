<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel;

use HeyPanel\Core\Framework\Struct\Struct;

/**
 * @internal
 *
 * @extends ClientApiResponse<Struct>
 */
class GenericClientApiResponse extends ClientApiResponse
{
    public function __construct(
        int $code,
        Struct $object,
    ) {
        $this->setStatusCode($code);

        parent::__construct($object);
    }
}
