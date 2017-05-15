<?php
namespace Beaplat\Traffic\Facades;

use Illuminate\Support\Facades\Facade;

class Traffic extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'traffic';
    }
}