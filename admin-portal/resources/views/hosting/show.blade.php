<x-app-layout :title="$account->domain">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
            <div class="flex justify-between items-start">
                <h2 class="font-semibold text-gray-700">Account Details</h2>
                <div class="flex gap-2">
                    <a href="{{ route('hosting.edit', $account->id) }}"
                       class="text-xs text-blue-600 hover:underline">Edit</a>
                </div>
            </div>
            <p><span class="text-gray-500">Customer:</span>
                <a href="{{ route('customers.show', $account->user_id) }}" class="text-blue-600 hover:underline">
                    {{ $account->user->name }}
                </a>
            </p>
            <p><span class="text-gray-500">Domain:</span> {{ $account->domain }}</p>
            <p><span class="text-gray-500">Plan:</span> {{ $account->plan }}</p>
            <p><span class="text-gray-500">cPanel Username:</span> {{ $account->cpanel_username }}</p>
            <p><span class="text-gray-500">Server:</span> {{ $account->server ?? '—' }}</p>
            <p><span class="text-gray-500">Status:</span> <x-badge :status="$account->status" /></p>
            @if($account->synergy_ref)
                <p><span class="text-gray-500">Synergy Ref:</span> {{ $account->synergy_ref }}</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-700">Actions</h2>
            @if($account->cpanel_url)
                <a href="{{ $account->cpanel_url }}" target="_blank"
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Open cPanel &rarr;
                </a>
            @endif
            <form method="POST" action="{{ route('hosting.update-status', $account->id) }}" class="flex gap-2">
                @csrf @method('PATCH')
                <select name="status" class="border-gray-300 rounded text-sm">
                    @foreach(['active','suspended','terminated','pending'] as $s)
                        <option value="{{ $s }}" {{ $account->status === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
                <button class="bg-gray-700 text-white px-3 py-1.5 rounded text-sm hover:bg-gray-800">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
