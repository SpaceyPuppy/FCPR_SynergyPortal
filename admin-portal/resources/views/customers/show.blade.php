<x-app-layout :title="$customer->name">
    <div class="flex gap-3 mb-6">
        <a href="{{ route('customers.edit', $customer->id) }}"
           class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Edit</a>
        <form method="POST" action="{{ route('customers.toggle-active', $customer->id) }}">
            @csrf @method('PATCH')
            <button class="border border-gray-300 px-4 py-2 rounded text-sm hover:bg-gray-50">
                {{ $customer->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile --}}
        <div class="bg-white rounded-lg shadow p-5 space-y-2 text-sm">
            <h2 class="font-semibold text-gray-700 mb-3">Profile</h2>
            <p><span class="text-gray-500">Email:</span> {{ $customer->email }}</p>
            <p><span class="text-gray-500">Company:</span> {{ $customer->company ?? '—' }}</p>
            <p><span class="text-gray-500">Phone:</span> {{ $customer->phone ?? '—' }}</p>
            <p><span class="text-gray-500">Status:</span>
                <x-badge :status="$customer->is_active ? 'active' : 'suspended'" />
            </p>
            <p><span class="text-gray-500">Joined:</span> {{ $customer->created_at->format('d M Y') }}</p>
        </div>

        {{-- Domains --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-700 text-sm">Domains ({{ $customer->domains->count() }})</h2>
                <a href="{{ route('domains.register') }}?customer_id={{ $customer->id }}" class="text-xs text-blue-600 hover:underline">+ Register</a>
            </div>
            <div class="divide-y text-sm">
                @forelse($customer->domains as $domain)
                    <div class="px-4 py-2 flex justify-between">
                        <a href="{{ route('domains.show', $domain->id) }}" class="text-blue-600 hover:underline">
                            {{ $domain->domain_name }}
                        </a>
                        <span class="text-gray-400 text-xs">{{ $domain->expiry_date?->format('d M Y') }}</span>
                    </div>
                @empty
                    <p class="px-4 py-3 text-gray-400">None.</p>
                @endforelse
            </div>
        </div>

        {{-- Hosting --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-700 text-sm">Hosting ({{ $customer->hostingAccounts->count() }})</h2>
                <a href="{{ route('hosting.create') }}?customer_id={{ $customer->id }}" class="text-xs text-blue-600 hover:underline">+ Add</a>
            </div>
            <div class="divide-y text-sm">
                @forelse($customer->hostingAccounts as $account)
                    <div class="px-4 py-2 flex justify-between">
                        <a href="{{ route('hosting.show', $account->id) }}" class="text-blue-600 hover:underline">
                            {{ $account->domain }}
                        </a>
                        <x-badge :status="$account->status" />
                    </div>
                @empty
                    <p class="px-4 py-3 text-gray-400">None.</p>
                @endforelse
            </div>
        </div>

        {{-- Invoices --}}
        <div class="bg-white rounded-lg shadow lg:col-span-3">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <h2 class="font-semibold text-gray-700 text-sm">Invoices ({{ $customer->invoices->count() }})</h2>
                <a href="{{ route('invoices.create') }}?customer_id={{ $customer->id }}" class="text-xs text-blue-600 hover:underline">+ Create Invoice</a>
            </div>
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-500">Invoice</th>
                        <th class="px-4 py-2 text-left text-gray-500">Due Date</th>
                        <th class="px-4 py-2 text-right text-gray-500">Amount</th>
                        <th class="px-4 py-2 text-center text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customer->invoices as $invoice)
                        <tr>
                            <td class="px-4 py-2">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">#{{ $invoice->id }}</a>
                            </td>
                            <td class="px-4 py-2 text-gray-600">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">${{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-4 py-2 text-center"><x-badge :status="$invoice->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">No invoices.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
