<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class SectorDTO implements DTOInputInterface
{
    public ?int $id = null;
    public string $name;
    public string $description;
    public string $location;
}
