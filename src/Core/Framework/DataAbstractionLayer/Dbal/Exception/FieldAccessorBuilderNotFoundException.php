<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class FieldAccessorBuilderNotFoundException extends HeyPanelHttpException
{
    public function __construct(string $field)
    {
        parent::__construct(
            'The field accessor builder for field {{ field }} was not found.',
            ['field' => $field]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__FIELD_ACCESSOR_BUILDER_NOT_FOUND';
    }
}
