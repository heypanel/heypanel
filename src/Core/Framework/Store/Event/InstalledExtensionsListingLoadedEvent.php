<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Event;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Store\Struct\ExtensionCollection;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 */
class InstalledExtensionsListingLoadedEvent extends Event
{
    public function __construct(public ExtensionCollection $extensionCollection, public readonly Context $context)
    {
    }
}
