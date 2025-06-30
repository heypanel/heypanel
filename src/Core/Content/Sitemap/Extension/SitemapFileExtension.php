<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Sitemap\Extension;

use HeyPanel\Core\Framework\Extensions\Extension;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 *
 * @extends Extension<Response>
 */
final class SitemapFileExtension extends Extension
{
    public const NAME = 'sitemap.get-file';

    /**
     * @internal
     */
    public function __construct(
        /**
         * @public
         *
         * @description Allows you to access to the current request
         */
        public readonly Request $request,

        /**
         * @public
         *
         * @description Allows you to access to the current customer/channel context
         */
        public readonly ChannelContext $context,

        /**
         * @public
         *
         * @description The file path of the requested sitemap file
         */
        public readonly string $filePath
    ) {
    }
}
