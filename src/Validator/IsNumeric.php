<?php
declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class IsNumeric extends Constraint
{

    public string $message = 'The value "{{ value }}" is not a valid integer or integer string.';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}