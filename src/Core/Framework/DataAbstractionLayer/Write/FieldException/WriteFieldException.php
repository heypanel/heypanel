<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Write\FieldException;

use HeyPanel\Core\Framework\HeyPanelException;

interface WriteFieldException extends HeyPanelException
{
    public function getPath(): string;
}
