<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>shinka.kz</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons8-покрышка-ios-17-filled-16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons8-покрышка-ios-17-filled-32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('icons8-покрышка-ios-17-filled-96.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="96x96" href="{{ asset('icons8-покрышка-ios-17-filled-96.png') }}">
        
        <!-- Meta tags -->
        <meta name="description" content="shinka.kz - поиск шиномонтажных сервисов">
        <meta name="keywords" content="шиномонтаж, шины, диски, автосервис, ремонт">
        <meta name="author" content="shinka.kz">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html> 