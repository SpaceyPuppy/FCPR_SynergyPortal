<x-app-layout :title="'Nameservers: ' . $domain->domain_name">
    <div class="max-w-lg bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('domains.update-nameservers', $domain->id) }}" class="space-y-3">
            @csrf @method('PATCH')
            <p class="text-sm text-gray-500 mb-2">Enter at least 2 nameservers for <strong>{{ $domain->domain_name }}</strong>.</p>
            @foreach(array_pad($domain->nameservers ?? [], 4, '') as $i => $ns)
                <div>
                    <label class="block text-xs text-gray-500">Nameserver {{ $i + 1 }} {{ $i < 2 ? '*' : '' }}</label>
                    <input type="text" name="nameservers[]" value="{{ old("nameservers.{$i}", $ns) }}"
                           placeholder="ns{{ $i+1 }}.example.com" {{ $i < 2 ? 'required' : '' }}
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
            @endforeach
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Update Nameservers
                </button>
                <a href="{{ route('domains.show', $domain->id) }}"
                   class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
