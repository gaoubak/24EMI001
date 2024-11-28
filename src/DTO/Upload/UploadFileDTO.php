<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class UploadFileDTO implements DTOInputInterface
{
    public ?int $id = null;
    public string $url;
    public string $type;
    public ?int $interventionId = null;
}
