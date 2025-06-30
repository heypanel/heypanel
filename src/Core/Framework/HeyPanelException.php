<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework;

interface HeyPanelException extends \Throwable
{
    public function getErrorCode(): string;

    /**
     * @return array<string|int, mixed|null>
     */
    public function getParameters(): array;
}
