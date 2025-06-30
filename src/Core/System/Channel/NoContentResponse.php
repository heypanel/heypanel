<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel;

use HeyPanel\Core\Framework\Struct\ArrayStruct;

/**
 * @extends ClientApiResponse<ArrayStruct<array{}>>
 */
class NoContentResponse extends ClientApiResponse
{
    public function __construct()
    {
        parent::__construct(new ArrayStruct());
        $this->setStatusCode(self::HTTP_NO_CONTENT);
    }
}
