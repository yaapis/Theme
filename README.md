# Theme support for Laravel 4

Inspired by [bigecko/laravel-theme](https://github.com/harryxu/laravel-theme).
Themes are stored inside default laravel's app/views folder

## Installation
Require this package in your composer.json:

~~~json
"yaap/theme": "dev-master"
~~~

And add the ServiceProvider to the providers array in app/config/app.php

~~~php
'YAAP\Theme\ThemeServiceProvider',
~~~

Publish config using artisan CLI (if you want to overwrite default config).

~~~bash
php artisan config:publish yaap/theme
~~~

You can register the facade in the `aliases` key of your `app/config/app.php` file.

~~~php
'aliases' => array(
    'Theme' => 'YAAP\Theme\Facades\Theme'
)
~~~


## Package config

~~~php
	return array(
        'path'          => app_path('views/themes'),
        'assets_path'   => 'assets/themes',
    );
~~~


## Theme config

~~~php
	return array(
        'name'         => 'default',
        'parent_theme' => null,
    );
~~~



##Usage

### Structure

```
├── app/views/
    └── themes/
        ├── default/
        |   ├── layouts/
            ├── partials/
            ├── views/
	        |   └── hello.blade.php
	        └── config.php

        └── admin/

    ├── emails/
    |   └── notify.blade.php
    └── errors/

├── public/assets/
    └── themes/
		└── default/
			├── css/
			|	└── styles.css
			└──	images/
                └── icon.png
```

### Create theme with artisan CLI

The first time you have to create theme "default" structure, using the artisan command:

~~~bash
php artisan theme:create default
~~~

To delete an existing theme, use the command:

~~~bash
php artisan theme:destroy default
~~~

###Init theme

~~~php
Theme::init($name)
~~~

This will add to views find path:
* app/views/{$name}
* app/views/{$name}/views

### Making view

~~~php
View::make('hello');
View::make('emails.notify');
~~~

### Assets
Assets can be nested too.
Asset url can be automatically with version.

~~~css
<link rel="stylesheet" href="{{ Theme::asset('css/styles.css', null, true) }}"/>
<link rel="stylesheet" href="{{ Theme::asset('css/ie.css', null, 'v1') }}"/>
~~~

The first one will get version from filemtime, the second one - from params


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
