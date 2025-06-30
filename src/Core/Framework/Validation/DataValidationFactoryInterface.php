<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Validation;

use HeyPanel\Core\System\Channel\ChannelContext;

interface DataValidationFactoryInterface
{
    public function create(ChannelContext $context): DataValidationDefinition;

    public function update(ChannelContext $context): DataValidationDefinition;
}
