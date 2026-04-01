<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FCPR Customer Portal' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-56 bg-blue-800 text-blue-100 flex flex-col flex-shrink-0">
        <div class="p-5 border-b border-blue-700">
            <span class="text-white font-bold text-lg">FCPR Portal</span>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-700 {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200' }}">
                Dashboard
            </a>
            <a href="{{ route('hosting.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-700 {{ request()->routeIs('hosting.*') ? 'bg-blue-700 text-white' : 'text-blue-200' }}">
                Hosting
            </a>
            <a href="{{ route('domains.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-700 {{ request()->routeIs('domains.*') ? 'bg-blue-700 text-white' : 'text-blue-200' }}">
                Domains
            </a>
            <a href="{{ route('ssl.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-700 {{ request()->routeIs('ssl.*') ? 'bg-blue-700 text-white' : 'text-blue-200' }}">
                SSL Certificates
            </a>
            <a href="{{ route('dns.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-700 {{ request()->routeIs('dns.*') ? 'bg-blue-700 text-white' : 'text-blue-200' }}">
                DNS
            </a>
            <a href="{{ route('invoices.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-blue-700 {{ request()->routeIs('invoices.*') ? 'bg-blue-700 text-white' : 'text-blue-200' }}">
                Invoices
            </a>
        </nav>
        <div class="p-4 border-t border-blue-700 text-xs text-blue-300">
            <a href="{{ route('account.show') }}" class="block mb-1 hover:text-white">{{ auth()->user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-blue-300 hover:text-white underline">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm px-6 py-4">
            <h1 class="text-xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            @include('components.alert')
            {{ $slot }}
        </main>
    </div>

</div>

</body>
</html>
