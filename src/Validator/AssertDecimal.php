<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AssertDecimal extends Constraint
{
    public function __construct(
        public string $message = 'La valeur "{{ value }}" doit être une valeur décimale.',
        public ?float $min = null,
        public ?float $max = null,
        $groups = null,
        $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy(): string
    {
        return AssertDecimalValidator::class;
    }
}