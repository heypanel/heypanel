<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\ChannelAware;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\Framework\Event\CustomerAware;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Customer\CustomerEntity;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerLoginEvent extends Event implements ChannelAware, HeyPanelChannelEvent, CustomerAware
{
    final public const EVENT_NAME = 'system.customer.login';

    public function __construct(
        private readonly ChannelContext $channelContext,
        private readonly CustomerEntity $customer,
        private readonly string $contextToken
    ) {
    }

    public function getChannelId(): string
    {
        return $this->channelContext->getChannelId();
    }

    public function getCustomerId(): string
    {
        return $this->customer->getId();
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getContext(): Context
    {
        return $this->channelContext->getContext();
    }

    public function getContextToken(): string
    {
        return $this->contextToken;
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }
}
