<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\ChannelAware;
use HeyPanel\Core\Framework\Event\CustomerAware;
use HeyPanel\Core\Framework\Event\EventData\EntityType;
use HeyPanel\Core\Framework\Event\EventData\EventDataCollection;
use HeyPanel\Core\Framework\Event\EventData\MailRecipientStruct;
use HeyPanel\Core\Framework\Event\FlowEventAware;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\Framework\Event\MailAware;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Customer\CustomerDefinition;
use HeyPanel\Core\System\Customer\CustomerEntity;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerRegisterEvent extends Event implements ChannelAware, HeyPanelChannelEvent, CustomerAware, MailAware, FlowEventAware
{
    public const EVENT_NAME = 'system.customer.register';

    private ?MailRecipientStruct $mailRecipientStruct = null;

    public function __construct(
        private readonly ChannelContext $salesChannelContext,
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
        return $this->salesChannelContext;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('customer', new EntityType(CustomerDefinition::class));
    }

    public function getMailStruct(): MailRecipientStruct
    {
        if (!$this->mailRecipientStruct instanceof MailRecipientStruct) {
            $this->mailRecipientStruct = new MailRecipientStruct([
                $this->customer->getEmail() => $this->customer->getNickname(),
            ]);
        }

        return $this->mailRecipientStruct;
    }

    public function getChannelId(): string
    {
        return $this->salesChannelContext->getChannelId();
    }

    public function getCustomerId(): string
    {
        return $this->customer->getId();
    }
}
