<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AssertDecimalValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void {
        $isValid = true;
        
        if ($value !== null && !preg_match('/^\d+(\.|\,)?\d{0,2}$/', $value, $matches)) {
            $isValid = false;
        }
        
        if (!$isValid) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}