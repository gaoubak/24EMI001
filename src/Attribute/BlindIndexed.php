<?php

namespace App\Attribute;

class BlindIndexed implements PropertyAttribute
{
    public function __construct(public string $targetPropertyName)
    {
        
    }

    public function getTargetPropertyName(): string
    {
        return $this->targetPropertyName;
    }
}