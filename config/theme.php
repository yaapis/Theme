<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Path to directory with themes
    |--------------------------------------------------------------------------
    |
    | The directory with your themes.
    |
    */

    'path' => base_path('themes'),

    /*
    |--------------------------------------------------------------------------
    | Path to directory with assets build
    |--------------------------------------------------------------------------
    |
    | The directory with assets build in public directory.
    |
    */

    'assets_path' => 'themes',

    /*
    |--------------------------------------------------------------------------
    | A pieces of theme collections
    |--------------------------------------------------------------------------
    |
    | Inside a theme path we need to set up directories to
    | keep "layouts", "assets" and "partials".
    |
    */

    'containerDir' => [
        'assets' => 'assets',
        'lang' => 'lang',
        'layout' => 'views/layouts',
        'partial' => 'views/partials',
        'view' => 'views',
    ],
];
