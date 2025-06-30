<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\TypeDetector;

use HeyPanel\Core\Content\Media\File\MediaFile;
use HeyPanel\Core\Content\Media\MediaType\MediaType;
use HeyPanel\Core\Content\Media\MediaType\SpatialObjectType;

/**
 * @experimental stableVersion:v6.8.0 feature:SPATIAL_BASES
 */
class SpatialObjectTypeDetector implements TypeDetectorInterface
{
    protected const SUPPORTED_FILE_EXTENSIONS = [
        'glb' => [],
    ];

    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType): ?MediaType
    {
        $fileExtension = mb_strtolower($mediaFile->getFileExtension());
        if (!\array_key_exists($fileExtension, self::SUPPORTED_FILE_EXTENSIONS)) {
            return $previouslyDetectedType;
        }

        if ($previouslyDetectedType === null) {
            $previouslyDetectedType = new SpatialObjectType();
        }

        $previouslyDetectedType->addFlags(self::SUPPORTED_FILE_EXTENSIONS[$fileExtension]);

        return $previouslyDetectedType;
    }
}
