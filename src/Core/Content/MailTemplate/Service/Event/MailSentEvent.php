<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\MailTemplate\Service\Event;

use HeyPanel\Core\Content\Flow\Dispatching\Action\FlowMailVariables;
use HeyPanel\Core\Content\Flow\Dispatching\Aware\ScalarValuesAware;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\EventData\ArrayType;
use HeyPanel\Core\Framework\Event\EventData\EventDataCollection;
use HeyPanel\Core\Framework\Event\EventData\ScalarValueType;
use HeyPanel\Core\Framework\Event\FlowEventAware;
use HeyPanel\Core\Framework\Log\LogAware;
use Monolog\Level;
use Symfony\Contracts\EventDispatcher\Event;

class MailSentEvent extends Event implements LogAware, ScalarValuesAware, FlowEventAware
{
    final public const EVENT_NAME = 'mail.sent';

    /**
     * @param array<string, mixed> $recipients
     * @param array<string, mixed> $contents
     */
    public function __construct(
        private readonly string $subject,
        private readonly array $recipients,
        private readonly array $contents,
        private readonly Context $context,
        private readonly ?string $eventName = null
    ) {
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('subject', new ScalarValueType(ScalarValueType::TYPE_STRING))
            ->add('contents', new ScalarValueType(ScalarValueType::TYPE_STRING))
            ->add('recipients', new ArrayType(new ScalarValueType(ScalarValueType::TYPE_STRING)));
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    /**
     * @return array<string, scalar|array<mixed>|null>
     */
    public function getValues(): array
    {
        return [
            FlowMailVariables::SUBJECT => $this->subject,
            FlowMailVariables::CONTENTS => $this->contents,
            FlowMailVariables::RECIPIENTS => $this->recipients,
        ];
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getLogData(): array
    {
        return [
            'eventName' => $this->eventName,
            'subject' => $this->subject,
            'recipients' => $this->recipients,
            'contents' => $this->contents,
        ];
    }

    public function getLogLevel(): Level
    {
        return Level::Info;
    }
}
