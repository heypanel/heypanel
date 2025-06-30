<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Theme;

use HeyPanel\Core\Framework\Adapter\Cache\Event\AddCacheTagEvent;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ThemeConfigValueAccessor
{
    /**
     * @var array<string, mixed>
     */
    private array $themeConfig = [];

    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractResolvedConfigLoader $themeConfigLoader,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    /**
     * @return string|bool|array<string, mixed>|float|int|null
     */
    public function get(string $key, ChannelContext $context, ?string $themeId)
    {
        $config = $this->getThemeConfig($context, $themeId);

        return $config[$key] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    private function getThemeConfig(ChannelContext $context, ?string $themeId): array
    {
        $key = $context->getChannelId() . $context->getDomainId() . $themeId;

        if (isset($this->themeConfig[$key])) {
            return $this->themeConfig[$key];
        }

        $themeConfig = [
            'breakpoint' => [
                'xs' => 0,
                'sm' => 576,
                'md' => 768,
                'lg' => 992,
                'xl' => 1200,
                'xxl' => 1400,
            ],
        ];

        if (!$themeId) {
            return $this->themeConfig[$key] = $this->flatten($themeConfig, null);
        }

        $this->dispatcher->dispatch(new AddCacheTagEvent(
            ThemeConfigCacheInvalidator::buildCacheTag($themeId)
        ));

        $themeConfig = array_merge(
            $themeConfig,
            [
                'assets' => [
                    'css' => [
                        '/css/all.css',
                    ],
                    'js' => [
                        '/js/all.js',
                    ],
                ],
            ],
            $this->themeConfigLoader->load($themeId, $context)
        );

        return $this->themeConfig[$key] = $this->flatten($themeConfig, null);
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    private function flatten(array $values, ?string $prefix): array
    {
        $prefix = $prefix ? $prefix . '.' : '';
        $flat = [];
        foreach ($values as $key => $value) {
            $isNested = \is_array($value) && !isset($value[0]);

            if (!$isNested) {
                $flat[$prefix . $key] = $value;

                continue;
            }

            $nested = $this->flatten($value, $prefix . $key);
            foreach ($nested as $nestedKey => $nestedValue) {
                $flat[$nestedKey] = $nestedValue;
            }
        }

        return $flat;
    }
}
