<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo;

use HeyPanel\Core\System\Channel\ChannelContext;

interface SeoUrlPlaceholderHandlerInterface
{
    /**
     * @param string $name
     * @param array<mixed> $parameters
     */
    public function generate($name, array $parameters = []): string;

    public function replace(string $content, string $host, ChannelContext $context): string;
}
