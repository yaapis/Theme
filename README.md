# Theme support for Laravel 4

Inspired by [bigecko/laravel-theme](https://github.com/harryxu/laravel-theme), but without any asset manager.
Themes are stored inside default laravel's app/views folder

## Installation
Require this package in your composer.json:

    "yaap/theme": "dev-master"

And add the ServiceProvider to the providers array in app/config/app.php

    'YAAP\Theme\ThemeServiceProvider',

Publish config using artisan CLI (if you want to overwrite default config).

    'php artisan config:publish yaap/theme'

You can register the facade in the `aliases` key of your `app/config/app.php` file.

~~~
'aliases' => array(
    'Theme' => 'YAAP\Theme\Facades\Theme'
)
~~~


##Config

    return array(
        'path' => app_path('views/themes'),
    );


##Usage

### Structure

```
├── app/views/
    └── themes/
        ├── default/
        |   ├── layouts/
        |   ├── partials/
        |   └── views/
	    |       └── hello.blade.php
        └── admin/
    ├── emails/
    |   └── notify.blade.php
    └── errors/

```

###Init theme

    Theme::init($name)
    Theme::init($name)

This will add to views find path:
* app/views/{$name}
* app/views/{$name}/views

### Making view
	View::make('hello');
	View::make('emails.notify');

###Blade templates

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

###Fallback capability

You still able to use default `View::make('emails.notify')` whitch stored outside the themes directory
