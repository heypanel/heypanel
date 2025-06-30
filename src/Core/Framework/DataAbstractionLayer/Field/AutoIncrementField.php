<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Field;

use HeyPanel\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;

class AutoIncrementField extends IntField
{
    public function __construct()
    {
        parent::__construct('auto_increment', 'autoIncrement');

        $this->addFlags(new WriteProtected());
    }
}
