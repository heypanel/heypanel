<?php

declare(strict_types=1);

namespace HeyPanel\Core\Content\Rule;

use HeyPanel\Core\Framework\DataAbstractionLayer\Exception\UnsupportedCommandTypeException;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use HeyPanel\Core\Framework\HttpException;

class RuleException extends HttpException
{
    public static function unsupportedCommandType(WriteCommand $command): HttpException
    {
        return new UnsupportedCommandTypeException($command);
    }
}
