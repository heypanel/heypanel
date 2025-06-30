<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Context;

/**
 * @codeCoverageIgnore
 */
final readonly class LanguageInfo
{
    public function __construct(
        public string $name,
        public string $localeCode,
    ) {
    }
}
