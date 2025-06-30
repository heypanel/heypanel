<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Rule\Exception;

use HeyPanel\Core\Checkout\Cart\CartException;
use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 *
 * @deprecated tag:v6.8.0 - reason:remove-exception - Will be removed, use CartException::unsupportedValue() or CustomerException::unsupportedValue() or RuleException::unsupportedValue() instead
 */
class UnsupportedValueException extends HeyPanelHttpException
{
    public function __construct(
        protected string $type,
        protected string $class
    ) {
        parent::__construct(
            'Unsupported value of type {{ type }} in {{ class }}',
            ['type' => $type, 'class' => $class]
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__RULE_VALUE_NOT_SUPPORTED';
    }
}
