<?php

namespace App\DTO\Search;

use App\DTO\Input\DTOInputInterface;

class SearchDTO implements DTOInputInterface
{
    public int $limit = 10;
    public int $offset = 0;
}