<?php
declare(strict_types=1);

namespace GorillaDash\OutlookFormatter;

use Illuminate\Support\Facades\Facade;

class FormatterFacade extends Facade
{
    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'OutlookFormatter';
    }
}
