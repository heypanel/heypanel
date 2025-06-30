<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\MailTemplate;

use HeyPanel\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;

class MailTemplateException extends HttpException
{
    public const MAIL_INVALID_TEMPLATE_CONTENT = 'CONTENT__INVALID_MAIL_TEMPLATE_CONTENT';

    public static function invalidMailTemplateContent(): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::MAIL_INVALID_TEMPLATE_CONTENT,
            'Invalid Mail Template content under "mailTemplate.contentHtml" parameter, please send the plain template as string.'
        );
    }
}
