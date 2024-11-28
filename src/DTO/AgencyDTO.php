<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class AgencyDTO implements DTOInputInterface
{
    public ?int $id = null;
    public string $email;
    public string $name;
    public string $clientContact;
    public string $outsourcing;
    public string $prospect;
    public ?string $type = null;
    public ?string $address = null;
}
