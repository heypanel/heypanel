<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Requirement\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

abstract class RequirementException extends HeyPanelHttpException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_FAILED_DEPENDENCY;
    }
}
