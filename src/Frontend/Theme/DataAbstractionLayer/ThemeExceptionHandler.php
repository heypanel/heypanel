<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\DataAbstractionLayer;

use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\ExceptionHandlerInterface;
use HeyPanel\Frontend\Theme\Exception\ThemeException;

/**
 * @internal
 */
class ThemeExceptionHandler implements ExceptionHandlerInterface
{
    public function getPriority(): int
    {
        return ExceptionHandlerInterface::PRIORITY_DEFAULT;
    }

    public function matchException(\Throwable $e): ?\Throwable
    {
        if (preg_match(
            '/SQLSTATE\[23000]: Integrity constraint violation: 1451.*CONSTRAINT `fk.theme_media.media_id` FOREIGN KEY \(`media_id`\) REFERENCES `media` \(`id`\)/',
            $e->getMessage(),
        )) {
            return ThemeException::themeMediaStillInUse();
        }

        return null;
    }
}
