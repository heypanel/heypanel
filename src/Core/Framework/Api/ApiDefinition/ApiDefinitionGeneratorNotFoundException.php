<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\ApiDefinition;

use HeyPanel\Core\Framework\HeyPanelHttpException;
use Symfony\Component\HttpFoundation\Response;

class ApiDefinitionGeneratorNotFoundException extends HeyPanelHttpException
{
    public function __construct(string $format)
    {
        parent::__construct(
            'A definition generator for format "{{ format }}" was not found.',
            ['format' => $format]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__API_DEFINITION_GENERATOR_NOT_SUPPORTED';
    }
}
