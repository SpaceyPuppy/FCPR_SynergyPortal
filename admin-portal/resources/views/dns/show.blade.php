<x-app-layout :title="'DNS: ' . $domainModel->domain_name">

    {{-- Add record --}}
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <h2 class="font-semibold text-gray-700 mb-3 text-sm">Add DNS Record</h2>
        <form method="POST" action="{{ route('dns.store', $domainModel->domain_name) }}"
              class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @csrf
            <select name="type" required class="border-gray-300 rounded text-sm">
                @foreach(['A','AAAA','CNAME','MX','TXT','NS','SRV'] as $t)
                    <option>{{ $t }}</option>
                @endforeach
            </select>
            <input type="text" name="host" placeholder="Host / Name" required
                   class="border-gray-300 rounded text-sm">
            <input type="text" name="content" placeholder="Value / Content" required
                   class="border-gray-300 rounded text-sm col-span-2">
            <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-700">
                Add
            </button>
        </form>
    </div>

    {{-- Records table --}}
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
                                  action="{{ route('dns.destroy', [$domainModel->domain_name, $record->id]) }}"
                                  onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No DNS records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
