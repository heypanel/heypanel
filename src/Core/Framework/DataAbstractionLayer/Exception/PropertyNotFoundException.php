<?php

declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Exception;

use HeyPanel\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Symfony\Component\HttpFoundation\Response;

class PropertyNotFoundException extends DataAbstractionLayerException
{
    public function __construct(string $property, string $entityClassName)
    {
        parent::__construct(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::PROPERTY_NOT_FOUND,
            'Property "{{ property }}" does not exist in entity "{{ entityClassName }}".',
            ['property' => $property, 'entityClassName' => $entityClassName]
        );
    }
}
