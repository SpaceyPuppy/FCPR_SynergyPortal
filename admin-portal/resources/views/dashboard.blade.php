<x-app-layout title="Dashboard">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">Customers</p>
            <p class="text-3xl font-bold text-gray-800">{{ $customerCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">Active Hosting</p>
            <p class="text-3xl font-bold text-gray-800">{{ $activeHostingCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">Domains</p>
            <p class="text-3xl font-bold text-gray-800">{{ $domainCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">Unpaid Invoices</p>
            <p class="text-3xl font-bold text-red-600">{{ $unpaidInvoiceCount }}</p>
            @if($unpaidInvoiceTotal > 0)
                <p class="text-xs text-gray-500 mt-1">${{ number_format($unpaidInvoiceTotal, 2) }} outstanding</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Expiring Domains --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-5 py-4 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-700">Domains Expiring Soon</h2>
                <a href="{{ route('domains.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
            </div>
            <div class="divide-y">
                @forelse($expiringDomains as $domain)
                    <div class="px-5 py-3 flex justify-between items-center text-sm">
                        <div>
                            <a href="{{ route('domains.show', $domain->id) }}" class="font-medium text-blue-600 hover:underline">
                                {{ $domain->domain_name }}
                            </a>
                            <p class="text-xs text-gray-500">{{ $domain->user->name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-red-600 text-xs font-medium">
                            {{ $domain->expiry_date->format('d M Y') }}
                        </span>
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-gray-400">No domains expiring in the next 30 days.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Customers + API Status --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-5 py-4 border-b flex justify-between items-center">
                    <h2 class="font-semibold text-gray-700">Recent Customers</h2>
                    <a href="{{ route('customers.create') }}" class="text-xs text-blue-600 hover:underline">+ Add new</a>
                </div>
                <div class="divide-y">
                    @forelse($recentCustomers as $customer)
                        <div class="px-5 py-3 flex justify-between items-center text-sm">
                            <div>
                                <a href="{{ route('customers.show', $customer->id) }}" class="font-medium text-blue-600 hover:underline">
                                    {{ $customer->name }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                            </div>
                            <x-badge :status="$customer->is_active ? 'active' : 'suspended'" />
                        </div>
                    @empty
                        <p class="px-5 py-4 text-sm text-gray-400">No customers yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-700 mb-3">Synergy Wholesale API</h2>
                @if($apiBalance !== null)
                    <div class="flex items-center gap-2 text-sm text-green-700">
                        <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
                        Connected &mdash; Balance: <strong>${{ number_format($apiBalance, 2) }}</strong>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-sm text-red-600">
                        <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                        API unavailable or credentials not configured.
                    </div>
                @endif
                <a href="{{ route('synergy.index') }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">
                    API status &rarr;
                </a>
            </div>
        </div>

    </div>

</x-app-layout>
