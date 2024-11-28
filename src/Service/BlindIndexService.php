<?php

namespace App\Service;

class BlindIndexService
{
    public function __construct(private string $blindIndexKey)
    {

    }

    public function getBlindIndex($value): string
    {
        return hash_hmac('ripemd160', strtolower($value), $this->blindIndexKey);
    }
}