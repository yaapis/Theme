<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>%theme_name% theme</title>
    <link rel="stylesheet" href="{{ mix('/themes/%theme_name%/css/app.min.css') }}"/>
</head>

<body>

<div>@include('partials.header')</div>

<div>@yield('content')</div>

<div>@include('partials.footer')</div>

<script type="text/javascript" src="{{ mix('/themes/%theme_name%/js/app.min.js') }}"></script>

</body>
</html>
