<?php declare(strict_types=1);

namespace HeyPanel\Core\System\User\Recovery;

use HeyPanel\Core\Content\Flow\Dispatching\Action\FlowMailVariables;
use HeyPanel\Core\Content\Flow\Dispatching\Aware\ScalarValuesAware;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\EventData\EntityType;
use HeyPanel\Core\Framework\Event\EventData\EventDataCollection;
use HeyPanel\Core\Framework\Event\EventData\MailRecipientStruct;
use HeyPanel\Core\Framework\Event\EventData\ScalarValueType;
use HeyPanel\Core\Framework\Event\FlowEventAware;
use HeyPanel\Core\Framework\Event\MailAware;
use HeyPanel\Core\Framework\Event\UserAware;
use HeyPanel\Core\System\User\Aggregate\UserRecovery\UserRecoveryDefinition;
use HeyPanel\Core\System\User\Aggregate\UserRecovery\UserRecoveryEntity;
use HeyPanel\Core\System\User\UserEntity;
use Symfony\Contracts\EventDispatcher\Event;

class UserRecoveryRequestEvent extends Event implements UserAware, MailAware, ScalarValuesAware, FlowEventAware
{
    final public const EVENT_NAME = 'user.recovery.request';

    private ?MailRecipientStruct $mailRecipientStruct = null;

    public function __construct(
        private readonly UserRecoveryEntity $userRecovery,
        private readonly string $resetUrl,
        private readonly Context $context
    ) {
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getUserRecovery(): UserRecoveryEntity
    {
        return $this->userRecovery;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('userRecovery', new EntityType(UserRecoveryDefinition::class))
            ->add('resetUrl', new ScalarValueType('string'))
        ;
    }

    /**
     * @return array<string, scalar|array<mixed>|null>
     */
    public function getValues(): array
    {
        return [
            FlowMailVariables::RESET_URL => $this->resetUrl,
        ];
    }

    public function getMailStruct(): MailRecipientStruct
    {
        if (!$this->mailRecipientStruct instanceof MailRecipientStruct) {
            /** @var UserEntity $user */
            $user = $this->userRecovery->getUser();

            $this->mailRecipientStruct = new MailRecipientStruct([
                $user->getEmail() => $user->getName(),
            ]);
        }

        return $this->mailRecipientStruct;
    }

    public function getChannelId(): ?string
    {
        return null;
    }

    public function getResetUrl(): string
    {
        return $this->resetUrl;
    }

    public function getUserId(): string
    {
        return $this->userRecovery->getId();
    }
}
