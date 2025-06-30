<?php declare(strict_types=1);

namespace HeyPanel\Core\System\SystemConfig\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidDomainException extends HeyPanelHttpException
{
    public function __construct(string $domain)
    {
        parent::__construct('Invalid domain \'{{ domain }}\'', ['domain' => $domain]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__INVALID_DOMAIN';
    }
}
