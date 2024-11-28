<?php

namespace App\DTO\Search;

use Symfony\Component\Validator\Constraints as Assert;

class UserSearchDTO extends SearchDTO
{
   public ?string $mainRoleType = null;
}