<x-app-layout :title="$domain->domain_name">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-700">Domain Info</h2>
            <p><span class="text-gray-500">Domain:</span> <strong>{{ $domain->domain_name }}</strong></p>
            <p><span class="text-gray-500">Expires:</span>
                <span class="{{ $domain->isExpiringSoon() ? 'text-red-600 font-medium' : '' }}">
                    {{ $domain->expiry_date?->format('d M Y') ?? '—' }}
                </span>
            </p>
            <p><span class="text-gray-500">Status:</span> <x-badge :status="$domain->status" /></p>
            <p><span class="text-gray-500">Auto-renew:</span> {{ $domain->auto_renew ? 'Enabled' : 'Disabled' }}</p>

            <form method="POST" action="{{ route('domains.toggle-auto-renew', $domain->id) }}" class="pt-2">
                @csrf @method('PATCH')
                <input type="hidden" name="auto_renew" value="{{ $domain->auto_renew ? '0' : '1' }}">
                <button class="border border-gray-300 px-3 py-1.5 rounded text-sm hover:bg-gray-50">
                    {{ $domain->auto_renew ? 'Disable Auto-renew' : 'Enable Auto-renew' }}
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-semibold text-gray-700 text-sm">Nameservers</h2>
                <a href="{{ route('domains.edit-nameservers', $domain->id) }}"
                   class="text-xs text-blue-600 hover:underline">Edit</a>
            </div>
            @forelse($domain->nameservers ?? [] as $ns)
                <p class="font-mono text-sm text-gray-700 py-1 border-b last:border-0">{{ $ns }}</p>
            @empty
                <p class="text-sm text-gray-400">No nameservers on record.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>
