<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class ResetPasswordDTO implements DTOInputInterface
{
    public string $email;
}