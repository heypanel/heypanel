<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Language;

use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use HeyPanel\Core\System\Language\Exception\LanguageForeignKeyDeleteException;

class LanguageExceptionHandler implements ExceptionHandlerInterface
{
    public function getPriority(): int
    {
        return ExceptionHandlerInterface::PRIORITY_LATE;
    }

    public function matchException(\Throwable $e): ?\Throwable
    {
        if (preg_match('/SQLSTATE\[23000\]:.*(1217|1216).*a foreign key constraint/', $e->getMessage())) {
            return new LanguageForeignKeyDeleteException($e);
        }

        return null;
    }
}
