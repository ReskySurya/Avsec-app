<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AVSEC App')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    @auth
        @include('layouts.navbar')
        <div class="flex">
            @include('layouts.sidebar')
            <main class="ml-64 flex-1 p-6 min-h-screen">
                @yield('content')
            </main>
        </div>
    @else
        <main class="py-4">
            @yield('content')
        </main>
    @endauth
</body>
</html>
