<?php

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RequiresEditableVersion
{
    public function __construct()
    {
    }
}
