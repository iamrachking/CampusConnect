<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('favicon.ico')}}" type="image/x-icon">
        <title>{{ config('app.name', 'Campus Connect') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Material Design Icons -->
        <link rel="stylesheet" href="{{ asset('cdn/mdi/css/materialdesignicons.min.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="fd-bg-silver">
        <div class="">
            {{ $slot }}
        </div>

        @livewireScripts
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
    
                @if (session('error'))
                    toastr.error("{{ session('error') }}");
                @endif
    
                @if (session('info'))
                    toastr.info("{{ session('info') }}");
                @endif
    
                @if (session('warning'))
                    toastr.warning("{{ session('warning') }}");
                @endif
            });
        </script>
    </body>
</html>
