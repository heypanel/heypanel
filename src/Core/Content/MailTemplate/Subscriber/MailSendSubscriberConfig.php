<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\MailTemplate\Subscriber;

use HeyPanel\Core\Framework\Struct\Struct;

class MailSendSubscriberConfig extends Struct
{
    protected bool $skip;

    /**
     * @var array<string>
     */
    protected array $mediaIds = [];

    /**
     * @param array<string> $mediaIds
     */
    public function __construct(
        bool $skip,
        array $mediaIds = []
    ) {
        $this->skip = $skip;
        $this->mediaIds = $mediaIds;
    }

    public function skip(): bool
    {
        return $this->skip;
    }

    public function setSkip(bool $skip): void
    {
        $this->skip = $skip;
    }

    /**
     * @return array<string>
     */
    public function getMediaIds(): array
    {
        return $this->mediaIds;
    }

    /**
     * @param array<string> $mediaIds
     */
    public function setMediaIds(array $mediaIds): void
    {
        $this->mediaIds = $mediaIds;
    }
}
