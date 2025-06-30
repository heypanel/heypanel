<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Validation;

use HeyPanel\Core\Framework\HeyPanelException;
use Symfony\Component\Validator\ConstraintViolationList;

interface ConstraintViolationExceptionInterface extends HeyPanelException
{
    public function getViolations(): ConstraintViolationList;
}
