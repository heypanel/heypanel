<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Mail\Service;

use Symfony\Component\Mime\Email;

abstract class AbstractMailFactory
{
    /**
     * @param array<string, string|null> $sender e.g. ['shopware@example.com' => 'HeyPanel AG']
     * @param array<string, string|null> $recipients e.g. ['shopware@example.com' => 'HeyPanel AG', 'symfony@example.com' => 'Symfony']
     * @param array<'text/plain'|'text/html', string> $contents e.g. ['text/plain' => 'Foo', 'text/html' => '<h1>Bar</h1>']
     * @param list<string> $attachments
     * @param array{
     *     attachmentsConfig?: MailAttachmentsConfig|null,
     *     recipientsCc?: string|array<string, string|null>,
     *     recipientsBcc?: string|array<string, string|null>,
     *     replyTo?: string|array<string, string|null>,
     *     returnPath?: string|array<string, string|null>,
     * } $additionalData e.g. ['recipientsCc' => ['shopware@example.com' => 'shopware', 'recipientsBcc' => 'shopware@example.com', 'replyTo' => 'reply@example.com', 'returnPath' => 'bounce@example.com']
     * @param list<array{content: resource|string, fileName: string|null, mimeType: string|null}>|null $binAttachments
     */
    abstract public function create(
        string $subject,
        array $sender,
        array $recipients,
        array $contents,
        array $attachments,
        array $additionalData,
        ?array $binAttachments = null
    ): Email;

    abstract public function getDecorated(): AbstractMailFactory;
}
