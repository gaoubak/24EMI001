<?php

namespace App\DTO;

use App\DTO\Input\DTOInputInterface;

class UserUploadedFileDTO implements DTOInputInterface
{
    public ?int $id = null;
    public int $userId;
    public int $uploadFileId;
}
