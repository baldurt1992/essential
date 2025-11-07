<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Essential Innovation') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" sizes="100x100" href="/images/cropped-logo-icon-web-100x100.jpg">
    <link rel="shortcut icon" type="image/jpeg" href="/images/cropped-logo-icon-web-100x100.jpg">
    <link rel="apple-touch-icon" sizes="100x100" href="/images/cropped-logo-icon-web-100x100.jpg">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app"></div>
</body>

</html>