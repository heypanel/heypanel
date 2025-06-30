<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Channel;

use HeyPanel\Core\Framework\Validation\DataBag\RequestDataBag;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\ContextTokenResponse;

abstract class AbstractLogoutRoute
{
    abstract public function getDecorated(): AbstractLogoutRoute;

    abstract public function logout(ChannelContext $context, RequestDataBag $data): ContextTokenResponse;
}
