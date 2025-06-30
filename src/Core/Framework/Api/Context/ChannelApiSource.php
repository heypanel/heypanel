<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Context;

use HeyPanel\Core\Framework\Struct\JsonSerializableTrait;

class ChannelApiSource implements ContextSource, \JsonSerializable
{
    use JsonSerializableTrait;

    final public const type = 'channel';

    public function __construct(private readonly string $channelId)
    {
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public static function getType(): string
    {
        return self::type;
    }
}
