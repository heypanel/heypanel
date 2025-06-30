<?php declare(strict_types=1);

namespace HeyPanel\Core\System\NumberRange\Exception;

use HeyPanel\Core\Framework\HeyPanelHttpException;

class IncrementStorageNotFoundException extends HeyPanelHttpException
{
    /**
     * @param array<string> $availableStorages
     */
    public function __construct(
        string $configuredStorage,
        array $availableStorages
    ) {
        parent::__construct(
            'The number range increment storage "{{ configuredStorage }}" is not available. Available storages are: "{{ availableStorages }}".',
            [
                'configuredStorage' => $configuredStorage,
                'availableStorages' => implode('", "', $availableStorages),
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INCREMENT_STORAGE_NOT_FOUND';
    }
}
