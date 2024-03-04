# Theme support for Laravel

Inspired by [bigecko/laravel-theme](https://github.com/harryxu/laravel-theme).
Themes are stored inside default laravel's resources folder

## Introduction

This package provides a simple way to manage themes in Laravel applications. 

For example, you can develop **multiple** themes for your application and easily **switch** between themes for different purposes.

## Requirements

This version requires [PHP](https://www.php.net/) 8.1 and supports [Laravel](https://laravel.com/) 10.

This package also provides support for [Laravel Mix](https://laravel-mix.com/) and [Vite](https://vitejs.dev/) configurations.

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

Config in `config/theme.php` file.

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
        'layout' => 'views/layouts',
        'partial' => 'views/partials',
        'view' => 'views',
    ],
];
```

### Theme config

Config in theme folder. Placeholder `%theme_name%` will be replaced with theme name on creation.

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

#### Create command

The first time you have to create theme `default` structure, using the artisan command:

```bash
php artisan theme:create default
```

By default, it will use `vite` as assets builder. If you want to use `laravel mix` instead, use the command:

or with laravel mix:

```bash
php artisan theme:create default mix
```

#### Destroy command

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
|   |   ├── views/
|   |   |   ├── layouts/
|   |   |   ├── partials/
|   |   |   └── hello.blade.php
|   |   └── config.php
|   ├── admin/
|   ├── views/
|       ├── emails/
|       |   └── notify.blade.php
|       └── hello.blade.php
```

## Init theme

To init and use theme in your application, add the following code to any `boot` method in application service provider (e.g. `AppServiceProvider`):

```php
use YAAP\Theme\Facades\ThemeLoader;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ThemeLoader::init('default');
    }
}
```

This will add to views find path:

* `themes/{$name}/views`

Lang files will be added as well:

* `themes/{$name}/lang`

### Making view

> [Laravel: Creating & Rendering Views](https://laravel.com/docs/10.x/views#creating-and-rendering-views)

```php
View::make('hello');
View::make('emails.notify');

// or

view('hello');
view('emails.notify');
```

### Assets

#### Vite

If you use **Vite**, ensure `vite.config.js` have specified the input path for theme

```javascript
import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'themes/default/assets/js/app.js', // for default theme
                // ...
            ],
            refresh: true,
        }),
    ],
});
```

Because **app.js** includes **app.scss** you can use the following code to include assets in your views:

```blade
<head>
    <!--...-->
    @vite([
        'themes/default/assets/js/app.js',
    ])
</head>
```

#### Laravel Mix

If you use **Laravel Mix**, ensure `webpack.mix.js` have specified mix configuration for theme

```javascript
const mix = require('laravel-mix');

mix.disableNotifications();
mix.browserSync({
    open: true,
    proxy: 'localhost:8000',
    files: [
        'app/**/*',
        'routes/**/*', 
        'themes/**/*', // manually add this line
    ]
});

// other configutraions...

// mix for default theme
mix.copyDirectory('themes/default/assets/img', 'public/themes/default/img');
mix.copyDirectory('themes/default/assets/fonts', 'public/themes/default/fonts');
// js
mix.js(['themes/default/assets/js/app.js'], 'public/themes/default/js/app.min.js')
// sass
mix.sass('themes/default/assets/sass/app.scss', 'public/themes/default/css/app.min.css')
```

Then you can use the following code to include assets in your views:

in the `<head>` tag

```blade
<head>
    <!--...-->
    <link rel="stylesheet" href="{{ mix('/themes/default/css/app.min.css') }}"/>
</head>
```

and before `</body>` tag

```blade
<body>
    <!--...-->
    <script type="text/javascript" src="{{ mix('/themes/default/js/app.min.js') }}"></script>
</body>
```

To use images, you can use the following code:

```blade
<img src="{{ mix('themes/default/img/icon.png') }}" alt="icon">
```

### Building layouts

#### Layouts using Components

> [Laravel: Blade Components](https://laravel.com/docs/10.x/blade#components)

#### Layouts using template inheritance

To build layouts we use [template inheritance](https://laravel.com/docs/10.x/blade#layouts-using-template-inheritance).
You can use `@extends` directive to specify a parent layout.

```blade
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

You still able to use default `View::make('emails.notify')` which is stored outside the `themes` directory.

## Can I hire you guys?

Yes! Say hi: [hello@hexide-digital.com](mailto:hello@hexide-digital.com)

We will be happy to work with you! Other [work we’ve done](https://hexide-digital.com/)

### Follow us

Stay up to date with the latest news! Follow us on [LinkedIn](https://www.linkedin.com/company/hexide-digital)
or [Facebook](https://www.facebook.com/hexide.digital)

## License

[MIT](https://github.com/yaapis/Theme/blob/master/LICENSE) license.
