<?php
declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsNumericValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsNumeric) {
            throw new UnexpectedTypeException($constraint, IsNumeric::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_numeric($value)) {
            // If the value is not numeric, handle it accordingly or skip processing.
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', 'non-numeric value')
                ->addViolation();
        } else if ((float) $value != (int) $value) {
            // The value is numeric but not an integer or a string that can be cast to an integer without loss.
            // Safe to cast to string since is_numeric guarantees a scalar that can be represented as a string.
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->addViolation();
        }
    }
}