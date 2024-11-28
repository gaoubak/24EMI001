<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class InterventionDTO implements DTOInputInterface
{
    public ?int $id = null;
    public string $status;
    public string $description;
    public int $agencyId;
    public int $sectorId;
    public int $scheduleId;
}
