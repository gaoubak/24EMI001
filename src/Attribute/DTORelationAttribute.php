<?php

namespace App\Attribute;

class DTORelationAttribute implements PropertyAttribute
{
    public function __construct(public string $class, public string $targetPropertyName)
    {

    }

    public function getTargetPropertyName(): string
    {
        return $this->targetPropertyName;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}