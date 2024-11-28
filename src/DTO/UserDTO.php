<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class UserDTO implements DTOInputInterface
{
    public ?int $id = null;
    public string $email;
    public ?int $agencyId = null;
    public ?string $phoneNumber = null;
    public ?int $photoId = null;
    public ?string $password = null;
    public ?string $name = null;
    public ?string $role = null;
}