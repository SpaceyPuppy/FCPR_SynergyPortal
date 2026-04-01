<x-app-layout title="Domains">
    <div class="flex gap-3 mb-4">
        <a href="{{ route('domains.check') }}" class="border border-gray-300 px-4 py-2 rounded text-sm hover:bg-gray-50">
            Check Availability
        </a>
        <a href="{{ route('domains.register') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            + Register Domain
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Domain</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Expires</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Auto-renew</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($domains as $domain)
                    <tr class="{{ $domain->isExpiringSoon() ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                        <td class="px-4 py-3 font-medium">{{ $domain->domain_name }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            <a href="{{ route('customers.show', $domain->user_id) }}" class="hover:underline">
                                {{ $domain->user->name ?? '—' }}
                            </a>
                        </td>
                        <td class="px-4 py-3 {{ $domain->isExpiringSoon() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            {{ $domain->expiry_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">{{ $domain->auto_renew ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 text-center"><x-badge :status="$domain->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('domains.show', $domain->id) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No domains yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $domains->links() }}</div>
</x-app-layout>
