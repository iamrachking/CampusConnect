<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CampusConnect') }}</title>
    <meta http-equiv="refresh" content="0; url={{ route('login') }}">
</head>
<body>
    <div style="text-align: center; margin-top: 50px; font-family: Arial, sans-serif;">
        <h1>Redirection vers CampusConnect...</h1>
        <p>Si vous n'êtes pas redirigé automatiquement, <a href="{{ route('login') }}">cliquez ici</a>.</p>
    </div>
</body>
</html>