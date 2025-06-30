<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Services;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Store\Struct\ReviewStruct;

/**
 * @internal
 */
abstract class AbstractExtensionStoreLicensesService
{
    abstract public function cancelSubscription(int $licenseId, Context $context): void;

    abstract public function rateLicensedExtension(ReviewStruct $rating, Context $context): void;

    abstract protected function getDecorated(): AbstractExtensionStoreLicensesService;
}
