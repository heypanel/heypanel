<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Plugin\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use HeyPanel\Core\Framework\Struct\Collection;

/**
 * @extends Collection<HeyPanelHttpException>
 */
class ExceptionCollection extends Collection
{
    public function getApiAlias(): string
    {
        return 'plugin_exception_collection';
    }

    protected function getExpectedClass(): ?string
    {
        return HeyPanelHttpException::class;
    }
}
