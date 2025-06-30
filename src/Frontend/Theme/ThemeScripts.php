<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Core\ChannelRequest;
use HeyPanel\Core\PlatformRequest;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
readonly class ThemeScripts
{
    /**
     * @internal
     */
    public function __construct(
        private RequestStack $requestStack,
        private ThemeRuntimeConfigService $themeRuntimeConfigService,
    ) {
    }

    /**
     * @return array<string>
     */
    public function getThemeScripts(): array
    {
        $request = $this->requestStack->getMainRequest();

        if ($request === null) {
            return [];
        }

        $themeId = $request->attributes->get(ChannelRequest::ATTRIBUTE_THEME_ID);

        if ($themeId === null) {
            return [];
        }

        $channelContext = $request->attributes->get(PlatformRequest::ATTRIBUTE_CHANNEL_CONTEXT_OBJECT);
        if (!$channelContext instanceof ChannelContext) {
            return [];
        }

        $runtimeConfig = $this->themeRuntimeConfigService->getResolvedRuntimeConfig($themeId);

        if ($runtimeConfig === null) {
            return [];
        }
        \assert($runtimeConfig->scriptFiles !== null);

        return $runtimeConfig->scriptFiles;
    }
}
