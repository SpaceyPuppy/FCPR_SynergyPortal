<x-app-layout title="Dashboard">

    {{-- Welcome + Stats --}}
    <div class="mb-6">
        <p class="text-gray-600 text-sm">Welcome back, <strong>{{ auth()->user()->name }}</strong>.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('hosting.index') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Hosting Accounts</p>
            <p class="text-3xl font-bold text-gray-800">{{ $hostingCount }}</p>
        </a>
        <a href="{{ route('domains.index') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Domains</p>
            <p class="text-3xl font-bold text-gray-800">{{ $domainCount }}</p>
        </a>
        <a href="{{ route('ssl.index') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">SSL Certificates</p>
            <p class="text-3xl font-bold text-gray-800">{{ $sslCount }}</p>
        </a>
        <a href="{{ route('invoices.index') }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Unpaid Invoices</p>
            <p class="text-3xl font-bold {{ $unpaidInvoices > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $unpaidInvoices }}</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Expiring Domains --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-700">Domains Expiring Soon</h2>
            </div>
            <div class="divide-y">
                @forelse($expiringDomains as $domain)
                    <div class="px-5 py-3 flex justify-between items-center text-sm">
                        <a href="{{ route('domains.show', $domain->id) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $domain->domain_name }}
                        </a>
                        <span class="text-red-600 text-xs font-medium">
                            Expires {{ $domain->expiry_date->format('d M Y') }}
                        </span>
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-gray-400">No domains expiring in the next 30 days.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Invoices --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-5 py-4 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-700">Recent Invoices</h2>
                <a href="{{ route('invoices.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            <div class="divide-y">
                @forelse($recentInvoices as $invoice)
                    <div class="px-5 py-3 flex justify-between items-center text-sm">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                            Invoice #{{ $invoice->id }}
                        </a>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600">${{ number_format($invoice->amount, 2) }}</span>
                            <x-badge :status="$invoice->status" />
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-gray-400">No invoices yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</x-app-layout>
