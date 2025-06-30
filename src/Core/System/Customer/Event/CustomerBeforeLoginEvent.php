<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Event;

use HeyPanel\Core\Content\Flow\Dispatching\Action\FlowMailVariables;
use HeyPanel\Core\Content\Flow\Dispatching\Aware\ScalarValuesAware;
use HeyPanel\Core\Content\MailTemplate\Exception\MailEventConfigurationException;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\ChannelAware;
use HeyPanel\Core\Framework\Event\EventData\EventDataCollection;
use HeyPanel\Core\Framework\Event\EventData\MailRecipientStruct;
use HeyPanel\Core\Framework\Event\EventData\ScalarValueType;
use HeyPanel\Core\Framework\Event\FlowEventAware;
use HeyPanel\Core\Framework\Event\HeyPanelChannelEvent;
use HeyPanel\Core\Framework\Event\MailAware;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerBeforeLoginEvent extends Event implements ChannelAware, HeyPanelChannelEvent, MailAware, ScalarValuesAware, FlowEventAware
{
    final public const EVENT_NAME = 'system.customer.before.login';

    public function __construct(
        private readonly ChannelContext $channelContext,
        private readonly string $email
    ) {
    }

    /**
     * @return array<string, scalar|array<mixed>|null>
     */
    public function getValues(): array
    {
        return [
            FlowMailVariables::EMAIL => $this->email,
        ];
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('email', new ScalarValueType(ScalarValueType::TYPE_STRING));
    }

    public function getMailStruct(): MailRecipientStruct
    {
        throw new MailEventConfigurationException('Data for mailRecipientStruct not available.', self::class);
    }
}
