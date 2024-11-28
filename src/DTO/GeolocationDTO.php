<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class GeolocationDTO implements DTOInputInterface
{
    public ?int $id = null;
    public string $location; 
    public int $intervenantId;
}
