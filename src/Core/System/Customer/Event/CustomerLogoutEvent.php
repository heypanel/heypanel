<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\ChannelAware;
use HeyPanel\Core\Framework\Event\CustomerAware;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Customer\CustomerEntity;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerLogoutEvent extends Event implements ChannelAware, HeyPanelChannelEvent, CustomerAware
{
    final public const EVENT_NAME = 'checkout.customer.logout';

    public function __construct(
        private readonly ChannelContext $channelContext,
        private readonly CustomerEntity $customer
    ) {
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }

    public function getContext(): Context
    {
        return $this->channelContext->getContext();
    }

    public function getChannelId(): string
    {
        return $this->channelContext->getChannelId();
    }

    public function getCustomerId(): string
    {
        return $this->customer->getId();
    }
}
