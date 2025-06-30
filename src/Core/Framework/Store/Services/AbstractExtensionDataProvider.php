<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Services;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\Store\Struct\ExtensionCollection;

/**
 * @internal
 */
abstract class AbstractExtensionDataProvider
{
    abstract public function getInstalledExtensions(Context $context, bool $loadCloudExtensions = true, ?Criteria $searchCriteria = null): ExtensionCollection;

    abstract protected function getDecorated(): AbstractExtensionDataProvider;
}
