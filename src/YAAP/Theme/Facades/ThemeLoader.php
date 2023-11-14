<?php

namespace YAAP\Theme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \YAAP\Theme\ThemeLoader
 * @see \YAAP\Theme\ThemeLoader
 */
class ThemeLoader extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'theme-loader';
    }
}
