<?php
declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Extension;

use HeyPanel\Core\Framework\Extensions\Extension;

/**
 * @extends Extension<string>
 *
 * @codeCoverageIgnore
 */
final class ResolveRemoteThumbnailUrlExtension extends Extension
{
    public const NAME = 'remote_thumbnail_url.resolve';

    /**
     * @internal heypanel owns the __constructor, but the properties are public API
     */
    public function __construct(
        public string $mediaUrl,
        public string $mediaPath,
        public string $width,
        public string $height,
        public string $pattern,
        public ?\DateTimeInterface $mediaUpdatedAt
    ) {
    }
}
