<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

use HeyPanel\Core\Framework\Event\EventData\MailRecipientStruct;

#[IsFlowEventAware]
interface MailAware
{
    public const MAIL_STRUCT = 'mailStruct';

    public const CHANNEL_ID = 'salesChannelId';

    public function getMailStruct(): MailRecipientStruct;

    public function getChannelId(): ?string;
}
