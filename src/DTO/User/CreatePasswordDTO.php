<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class CreatePasswordDTO implements DTOInputInterface
{
    public string $token;
    public string $password;
}