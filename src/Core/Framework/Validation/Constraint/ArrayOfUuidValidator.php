<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Validation\Constraint;

use HeyPanel\Core\Framework\FrameworkException;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Core\Framework\Validation\Constraint\ArrayOfUuid as ArrayOfUuidConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintValidator;

class ArrayOfUuidValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ArrayOfUuidConstraint) {
            throw FrameworkException::unexpectedType($constraint, ArrayOfUuidConstraint::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if ($value === null || empty($value)) {
            return;
        }

        if (!\is_array($value)) {
            $this->context->buildViolation(ArrayOfUuidConstraint::INVALID_TYPE_MESSAGE)
                ->setCode(Type::INVALID_TYPE_ERROR)
                ->addViolation();

            return;
        }

        foreach ($value as $uuid) {
            if (!\is_string($uuid) || !Uuid::isValid($uuid)) {
                $this->context->buildViolation(ArrayOfUuidConstraint::INVALID_MESSAGE)
                    ->setCode(ArrayOfUuidConstraint::INVALID_TYPE_CODE)
                    ->setParameter('{{ string }}', (string) $uuid)
                    ->addViolation();
            }
        }
    }
}
