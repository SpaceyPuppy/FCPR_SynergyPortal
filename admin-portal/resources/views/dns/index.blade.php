<x-app-layout title="DNS Management">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Domain</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Customer</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($domains as $domain)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $domain->domain_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $domain->user->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('dns.show', $domain->domain_name) }}" class="text-blue-600 hover:underline">
                                Manage DNS
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">No domains yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $domains->links() }}</div>
</x-app-layout>
