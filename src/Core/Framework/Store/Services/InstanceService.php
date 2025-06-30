<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Services;

/**
 * @internal
 */
class InstanceService
{
    public function __construct(
        private readonly string $heypanelVersion,
        private readonly ?string $instanceId
    ) {
    }

    public function getHeyPanelVersion(): string
    {
        if (str_ends_with($this->heypanelVersion, '-dev')) {
            return '___VERSION___';
        }

        return $this->heypanelVersion;
    }

    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }
}
