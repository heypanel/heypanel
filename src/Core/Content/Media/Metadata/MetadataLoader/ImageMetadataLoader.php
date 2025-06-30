<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Metadata\MetadataLoader;

use HeyPanel\Core\Content\Media\MediaType\ImageType;
use HeyPanel\Core\Content\Media\MediaType\MediaType;

class ImageMetadataLoader implements MetadataLoaderInterface
{
    /**
     * @internal
     */
    public function __construct()
    {
    }

    /**
     * @return array{width: int, height: int, type: int}|null
     */
    public function extractMetadata(string $filePath): ?array
    {
        $metadata = \getimagesize($filePath);
        if (\is_array($metadata)) {
            return [
                'width' => $metadata[0],
                'height' => $metadata[1],
                'type' => $metadata[2],
            ];
        }

        return null;
    }

    public function supports(MediaType $mediaType): bool
    {
        return $mediaType instanceof ImageType;
    }
}
