<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Mail\Message;

use HeyPanel\Core\Framework\MessageQueue\AsyncMessageInterface;

/**
 * @codeCoverageIgnore
 */
class SendMailMessage implements AsyncMessageInterface
{
    /**
     * @internal
     */
    public function __construct(public readonly string $mailDataPath)
    {
    }
}
