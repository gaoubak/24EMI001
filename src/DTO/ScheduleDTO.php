<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class ScheduleDTO implements DTOInputInterface
{
    public ?int $id = null;
    public string $startTime;
    public string $endTime;
    public int $status;
    public int $intervenantId;
    public int $interventionId;
}
