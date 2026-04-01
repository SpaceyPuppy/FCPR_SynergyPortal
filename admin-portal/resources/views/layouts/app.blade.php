<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FCPR Admin Portal' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col flex-shrink-0">
        <div class="p-5 border-b border-gray-700">
            <span class="text-white font-bold text-lg">FCPR Admin</span>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                Dashboard
            </a>
            <a href="{{ route('customers.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('customers.*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                Customers
            </a>
            <a href="{{ route('hosting.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('hosting.*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                Hosting
            </a>
            <a href="{{ route('domains.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('domains.*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                Domains
            </a>
            <a href="{{ route('ssl.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('ssl.*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                SSL Certificates
            </a>
            <a href="{{ route('dns.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('dns.*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                DNS
            </a>
            <a href="{{ route('invoices.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('invoices.*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                Invoices
            </a>
            <div class="pt-3 mt-3 border-t border-gray-700">
                <a href="{{ route('synergy.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('synergy.*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                    Synergy API
                </a>
            </div>
        </nav>
        <div class="p-4 border-t border-gray-700 text-xs text-gray-400">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button class="text-gray-400 hover:text-white underline">Logout</button>
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
