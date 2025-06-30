<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Util;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class PluginIdProvider
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $pluginRepo)
    {
    }

    public function getPluginIdByBaseClass(string $pluginBaseClassName, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('baseClass', $pluginBaseClassName));
        /** @var string $id */
        $id = $this->pluginRepo->searchIds($criteria, $context)->firstId();

        return $id;
    }
}
