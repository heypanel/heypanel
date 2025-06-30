<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Event;

use HeyPanel\Core\Content\Flow\Dispatching\Aware\ScalarValuesAware;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\EventData\EventDataCollection;
use HeyPanel\Core\Framework\Event\EventData\ScalarValueType;
use HeyPanel\Core\Framework\Event\FlowEventAware;
use Symfony\Contracts\EventDispatcher\Event;

class MediaUploadedEvent extends Event implements ScalarValuesAware, FlowEventAware
{
    public const EVENT_NAME = 'media.uploaded';

    public function __construct(
        private readonly string $mediaId,
        private readonly Context $context
    ) {
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('mediaId', new ScalarValueType(ScalarValueType::TYPE_STRING));
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getValues(): array
    {
        return [
            'mediaId' => $this->mediaId,
        ];
    }
}
