<?php

declare(strict_types=1);

namespace HeyPanel\Core\System\Snippet\Extension;

use HeyPanel\Core\Framework\Extensions\Extension;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @extends Extension<array<string, string>>
 */
final class FrontendSnippetsExtension extends Extension
{
    public const NAME = 'frontend.snippets';

    /**
     * @internal heypanel owns the __constructor, but the properties are public API
     *
     * @param array<string, string> $snippets
     */
    public function __construct(
        public array $snippets,
        public readonly string $locale,
        public readonly MessageCatalogueInterface $catalog,
        public readonly string $snippetSetId,
        public readonly ?string $fallbackLocale,
        public readonly ?string $channelId
    ) {
    }
}
