<x-app-layout :title="'Add DNS Record: ' . $domain->domain_name">
    <div class="max-w-lg bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('dns.store', $domain->domain_name) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Type *</label>
                <select name="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    @foreach(['A','AAAA','CNAME','MX','TXT','NS','SRV'] as $t)
                        <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Host / Name *</label>
                <input type="text" name="host" value="{{ old('host') }}" required
                       placeholder="@ or subdomain"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Content / Value *</label>
                <input type="text" name="content" value="{{ old('content') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">TTL (seconds)</label>
                <input type="number" name="ttl" value="{{ old('ttl', 3600) }}" min="60" max="86400"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Add Record
                </button>
                <a href="{{ route('dns.show', $domain->domain_name) }}"
                   class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
