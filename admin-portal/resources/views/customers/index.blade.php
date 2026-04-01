<x-app-layout title="Customers">
    <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-gray-500">{{ $customers->total() }} customer(s)</p>
        <a href="{{ route('customers.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            + New Customer
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Company</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Domains</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Hosting</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Invoices</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $customer->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $customer->company ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $customer->domains_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $customer->hosting_accounts_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $customer->invoices_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <x-badge :status="$customer->is_active ? 'active' : 'suspended'" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('customers.show', $customer->id) }}"
                               class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">No customers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
</x-app-layout>
