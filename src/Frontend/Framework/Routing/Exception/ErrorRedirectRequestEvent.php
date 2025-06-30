<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Framework\Routing\Exception;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelEvent;
use Symfony\Component\HttpFoundation\Request;

class ErrorRedirectRequestEvent implements HeyPanelEvent
{
    public function __construct(
        private readonly Request $request,
        private readonly \Throwable $exception,
        private readonly Context $context,
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
