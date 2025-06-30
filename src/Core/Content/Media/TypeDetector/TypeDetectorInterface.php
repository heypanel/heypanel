<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\TypeDetector;

use HeyPanel\Core\Content\Media\File\MediaFile;
use HeyPanel\Core\Content\Media\MediaType\MediaType;

interface TypeDetectorInterface
{
    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType): ?MediaType;
}
