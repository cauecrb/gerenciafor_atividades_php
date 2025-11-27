<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Gerenciador de Atividades') }}</title>
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @routes
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
