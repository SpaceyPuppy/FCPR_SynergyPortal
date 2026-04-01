<x-app-layout :title="'DNS: ' . $domain->domain_name">

    <div class="flex justify-end mb-4">
        <a href="{{ route('dns.create', $domain->domain_name) }}"
           class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            + Add Record
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Host</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Content</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">TTL</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-mono text-xs font-bold">{{ $record->type }}</td>
                        <td class="px-4 py-2 font-mono text-xs">{{ $record->host }}</td>
                        <td class="px-4 py-2 font-mono text-xs max-w-xs truncate">{{ $record->content }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $record->ttl }}</td>
                        <td class="px-4 py-2 text-right">
                            <form method="POST"
                                  action="{{ route('dns.destroy', [$domain->domain_name, $record->id]) }}"
                                  onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">No DNS records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
