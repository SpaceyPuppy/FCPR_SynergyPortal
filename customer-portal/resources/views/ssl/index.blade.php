<x-app-layout title="SSL Certificates">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Domain</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Expires</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($certificates as $cert)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $cert->domain }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cert->type }}</td>
                        <td class="px-4 py-3 {{ $cert->isExpiringSoon() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            {{ $cert->expiry_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center"><x-badge :status="$cert->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                            No SSL certificates on your account. Contact us to order one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $certificates->links() }}</div>
</x-app-layout>
