# Theme support for Laravel

Inspired by [bigecko/laravel-theme](https://github.com/harryxu/laravel-theme).
Themes are stored inside default laravel's resources folder

## Requirements

This version requires [PHP](https://www.php.net/) 8.1 and supports [Laravel](https://laravel.com/) 10.

> Currently, supported only for webpack with `laravel-mix`.

| Themes | L5.5               | L5.6               | L5.7               | L5.8               | L6                 | L7  | L8                 | L9                 | L10                |
|--------|--------------------|--------------------|--------------------|--------------------|--------------------|-----|--------------------|--------------------|--------------------|
| 2.4    | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: | :x:                | :x: | :x:                | :x:                | :x:                |
| 3.0    | :x:                | :x:                | :x:                | :x:                | :white_check_mark: | :x: | :x:                | :x:                | :x:                |
| 4.2    | :x:                | :x:                | :x:                | :x:                | :x:                | :x: | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| 5.0    | :x:                | :x:                | :x:                | :x:                | :x:                | :x: | :x:                | :x:                | :white_check_mark: |

## Installation

To get the latest version, simply require the project using [Composer](https://getcomposer.org/):

```bash
composer require "yaap/theme:^5.0"
```

or manually add line to `composer.json`

```json
{
  "require": {
    "yaap/theme": "^5.0"
  }
}
```

Optionally, publish config using artisan CLI (if you want to overwrite default config).

```bash
php artisan vendor:publish --provider="YAAP\Theme\ThemeServiceProvider"
```

## Configuration

### Package config

```php
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
        'layout' => 'layouts',
        'partial' => 'partials',
        'view' => 'views',
    ],
];
```

### Theme config

Config in theme folder

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Theme name
    |--------------------------------------------------------------------------
    |
    | Use in assets publishing etc.
    |
    */

    'name' => '%theme_name%',

    /*
    |--------------------------------------------------------------------------
    | Inherit from another theme
    |--------------------------------------------------------------------------
    |
    | Set up inherit from another if the file is not exists.
    |
    */

    'inherit' => null,

];
```

## Usage

### Create theme with artisan CLI

The first time you have to create theme `default` structure, using the artisan command:

```bash
php artisan theme:create default
```

In order to seed `webpack.mix.js` with custom rules add `--with-mix` option

```bash
php artisan theme:create default --with-mix
```

To delete an existing theme, use the command:

```bash
php artisan theme:destroy default
```

### Structure

Here is an example of the folder structure of project with theme

```
project-root
├── app/
<...>
├── public/
|   ├── index.php
|   └── themes/
|       └── default/
|           ├── js/
|           |   └── app.js
|           ├── css/
|           |   └── styles.css
|           └── images/
|               └── icon.png
├── resources/
<...>
├── themes/
|   ├── default/
|   |   ├── assets/        
|   |   ├── lang/        
|   |   ├── layouts/
|   |   ├── partials/
|   |   ├── views/
|   |   |   └── hello.blade.php
|   |   └── config.php
|   ├── admin/
|   ├── views/
|       ├── emails/
|       |   └── notify.blade.php
|       └── hello.blade.php
```

## Init theme

```php
\YAAP\Theme\Facades\ThemeLoader::init($name);
```

This will add to views find path:

* themes/{$name}
* themes/{$name}/views

Lang files will be added as well:

* themes/{$name}/lang

### Making view

> [Laravel: Creating & Rendering Views](https://laravel.com/docs/10.x/views#creating-and-rendering-views)

```php
View::make('hello');
View::make('emails.notify');
```

### Assets

Use laravel mix for assets.

In header

```html
<link rel="stylesheet" href="{{ mix('/themes/default/css/app.min.css') }}"/>
```

and in footer

```html
<script type="text/javascript" src="{{ mix('/themes/default/js/app.min.js') }}"></script>
```

### Blade templates

```
@extends('layouts.master')

@include('partials.header')

@section('content')
    <section id="main">
        <h1>HOME</h1>
    </section>
@stop

@include('partials.footer')
```

### Fallback capability

You still able to use default `View::make('emails.notify')` which is stored outside the themes directory.

## Can I hire you guys?

Yes! Say hi: [hello@hexide-digital.com](mailto:hello@hexide-digital.com)

We will be happy to work with you! Other [work we’ve done](https://hexide-digital.com/)

### Follow us

Stay up to date with the latest news! Follow us on [LinkedIn](https://www.linkedin.com/company/hexide-digital)
or [Facebook](https://www.facebook.com/hexide.digital)

## License

[MIT](https://github.com/yaapis/Theme/blob/master/LICENSE) license.
