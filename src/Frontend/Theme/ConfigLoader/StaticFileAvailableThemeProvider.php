<?php
declare(strict_types=1);

namespace HeyPanel\Frontend\Theme\ConfigLoader;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use League\Flysystem\FilesystemOperator;

class StaticFileAvailableThemeProvider extends AbstractAvailableThemeProvider
{
    final public const THEME_INDEX = 'theme-config/index.json';

    /**
     * @internal
     */
    public function __construct(private readonly FilesystemOperator $filesystem)
    {
    }

    public function getDecorated(): AbstractAvailableThemeProvider
    {
        throw new DecorationPatternException(self::class);
    }

    public function load(Context $context, bool $activeOnly): array
    {
        if (!$this->filesystem->fileExists(self::THEME_INDEX)) {
            throw new \RuntimeException('Cannot find theme configuration. Did you run bin/console theme:dump');
        }

        return json_decode((string) $this->filesystem->read(self::THEME_INDEX), true, 512, \JSON_THROW_ON_ERROR);
    }
}
