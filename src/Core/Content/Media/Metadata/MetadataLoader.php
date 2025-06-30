<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Metadata;

use HeyPanel\Core\Content\Media\File\MediaFile;
use HeyPanel\Core\Content\Media\MediaType\MediaType;
use HeyPanel\Core\Content\Media\Metadata\MetadataLoader\MetadataLoaderInterface;

class MetadataLoader
{
    /**
     * @internal
     *
     * @param MetadataLoaderInterface[] $metadataLoader
     */
    public function __construct(private readonly iterable $metadataLoader)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function loadFromFile(MediaFile $mediaFile, MediaType $mediaType): ?array
    {
        $metaData = [];
        foreach ($this->metadataLoader as $loader) {
            if ($loader->supports($mediaType)) {
                $metaData = $loader->extractMetadata($mediaFile->getFileName());
                break;
            }
        }

        if ($mediaFile->getHash()) {
            $metaData['hash'] = $mediaFile->getHash();
        }

        return $metaData ?: null;
    }
}
