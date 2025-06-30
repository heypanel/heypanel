<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\TypeDetector;

use HeyPanel\Core\Content\Media\File\MediaFile;
use HeyPanel\Core\Content\Media\MediaType\AudioType;
use HeyPanel\Core\Content\Media\MediaType\BinaryType;
use HeyPanel\Core\Content\Media\MediaType\ImageType;
use HeyPanel\Core\Content\Media\MediaType\MediaType;
use HeyPanel\Core\Content\Media\MediaType\VideoType;

class DefaultTypeDetector implements TypeDetectorInterface
{
    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType): ?MediaType
    {
        if ($previouslyDetectedType !== null) {
            return $previouslyDetectedType;
        }

        $mime = explode('/', $mediaFile->getMimeType());

        return match ($mime[0]) {
            'image' => new ImageType(),
            'video' => new VideoType(),
            'audio' => new AudioType(),
            default => new BinaryType(),
        };
    }
}
