<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>%theme_name% theme</title>
    <link rel="stylesheet" href="{{ mix('/themes/%theme_name%/css/app.min.css') }}"/>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png"/>
</head>

<body>

@include('partials.header')

@yield('content')

@include('partials.footer')

<script type="text/javascript" src="{{ mix('/themes/%theme_name%/js/app.min.js') }}"></script>

</body>
</html>
