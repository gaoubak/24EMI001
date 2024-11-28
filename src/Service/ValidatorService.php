<?php

namespace App\Service;

use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService {
    public function __construct(private ValidatorInterface $validator)
    {

    }

    public function validate($entity) {
        $errors = $this->validator->validate($entity);
        
        if (count($errors) > 0) {
            throw new ValidationFailedException($entity, $errors);
        }
    }
}