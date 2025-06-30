<?php declare(strict_types=1);

namespace HeyPanel\Core\DevOps\StaticAnalyze;

use HeyPanel\Core\Kernel;

/**
 * @internal
 */
class StaticAnalyzeKernel extends Kernel
{
    public function getCacheDir(): string
    {
        return \sprintf(
            '%s/var/cache/static_%s',
            $this->getProjectDir(),
            $this->getEnvironment(),
        );
    }
}
