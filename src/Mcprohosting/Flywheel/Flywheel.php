<?php

namespace Mcprohosting\Flywheel;

use Illuminate\Support\Facades\Facade;

class Flywheel extends Facade
{
    protected static function getFacadeAccessor() { return 'flywheel'; }
} 