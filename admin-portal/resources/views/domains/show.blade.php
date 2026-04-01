<x-app-layout :title="$domain->domain_name">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Details --}}
        <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-700">Domain Details</h2>
            <p><span class="text-gray-500">Customer:</span>
                <a href="{{ route('customers.show', $domain->user_id) }}" class="text-blue-600 hover:underline">
                    {{ $domain->user->name }}
                </a>
            </p>
            <p><span class="text-gray-500">Expires:</span>
                <span class="{{ $domain->isExpiringSoon() ? 'text-red-600 font-medium' : '' }}">
                    {{ $domain->expiry_date?->format('d M Y') ?? '—' }}
                </span>
            </p>
            <p><span class="text-gray-500">Status:</span> <x-badge :status="$domain->status" /></p>
            <p><span class="text-gray-500">Auto-renew:</span> {{ $domain->auto_renew ? 'Enabled' : 'Disabled' }}</p>
            @if($apiInfo)
                <p><span class="text-gray-500">Registrar lock:</span>
                    {{ $apiInfo->getDomainStatus() ?? '—' }}</p>
            @endif

            {{-- Actions --}}
            <div class="pt-3 space-y-2">
                <form method="POST" action="{{ route('domains.renew', $domain->id) }}" class="flex gap-2">
                    @csrf
                    <select name="years" class="border-gray-300 rounded text-sm">
                        @for($i=1;$i<=5;$i++)<option value="{{ $i }}">{{ $i }} yr</option>@endfor
                    </select>
                    <button class="bg-green-600 text-white px-3 py-1.5 rounded text-sm hover:bg-green-700">Renew</button>
                </form>

                <form method="POST" action="{{ route('domains.toggle-auto-renew', $domain->id) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="auto_renew" value="{{ $domain->auto_renew ? '0' : '1' }}">
                    <button class="border border-gray-300 px-3 py-1.5 rounded text-sm w-full text-left hover:bg-gray-50">
                        {{ $domain->auto_renew ? 'Disable Auto-renew' : 'Enable Auto-renew' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('domains.lock', $domain->id) }}">
                    @csrf
                    <button class="border border-gray-300 px-3 py-1.5 rounded text-sm w-full text-left hover:bg-gray-50">Lock Domain</button>
                </form>

                <form method="POST" action="{{ route('domains.unlock', $domain->id) }}">
                    @csrf
                    <button class="border border-gray-300 px-3 py-1.5 rounded text-sm w-full text-left hover:bg-gray-50">Unlock Domain</button>
                </form>
            </div>
        </div>

        {{-- Nameservers --}}
        <div class="bg-white rounded-lg shadow p-5 lg:col-span-2">
            <h2 class="font-semibold text-gray-700 mb-3">Nameservers</h2>
            <form method="POST" action="{{ route('domains.update-nameservers', $domain->id) }}" class="space-y-2">
                @csrf @method('PATCH')
                @foreach(array_pad($domain->nameservers ?? [], 4, '') as $i => $ns)
                    <input type="text" name="nameservers[]" value="{{ $ns }}"
                           placeholder="ns{{ $i+1 }}.example.com"
                           class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                @endforeach
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 mt-2">
                    Update Nameservers
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
