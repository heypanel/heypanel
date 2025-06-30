<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo;

use HeyPanel\Core\System\Channel\ChannelContext;

class HreflangLoaderParameter
{
    protected string $route;

    /**
     * @var array<string, mixed>
     */
    protected array $routeParameters;

    protected ChannelContext $channelContext;

    /**
     * @param array<string, mixed> $routeParameters
     */
    public function __construct(
        string $route,
        array $routeParameters,
        ChannelContext $channelContext
    ) {
        $this->route = $route;
        $this->routeParameters = $routeParameters;
        $this->channelContext = $channelContext;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }
}
