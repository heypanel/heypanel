<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class DecorationPatternException extends HeyPanelHttpException
{
    public function __construct(protected string $class)
    {
        parent::__construct(\sprintf(
            'The getDecorated() function of core class %s cannot be used. This class is the base class.',
            $class
        ));
    }

    public function getErrorCode(): string
    {
        return (string) Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
