<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\MailTemplate\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class MailTemplateRendererException extends HeyPanelHttpException
{
    public function __construct(string $twigMessage)
    {
        parent::__construct(
            'Failed rendering mail template using Twig: {{ errorMessage }}',
            ['errorMessage' => $twigMessage]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MAIL_TEMPLATING_FAILED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
