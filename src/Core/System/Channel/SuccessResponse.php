<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel;

use HeyPanel\Core\Framework\Struct\ArrayStruct;

/**
 * @extends ClientApiResponse<ArrayStruct<array{success: bool}>>
 */
class SuccessResponse extends ClientApiResponse
{
    public function __construct()
    {
        parent::__construct(new ArrayStruct(['success' => true]));
    }
}
