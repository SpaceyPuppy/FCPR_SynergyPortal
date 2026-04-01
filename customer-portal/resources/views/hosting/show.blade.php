<x-app-layout :title="$account->domain">
    <div class="max-w-lg bg-white rounded-lg shadow p-5 space-y-3 text-sm">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-gray-700">Account Details</h2>
            <x-badge :status="$account->status" />
        </div>
        <p><span class="text-gray-500">Domain:</span> {{ $account->domain }}</p>
        <p><span class="text-gray-500">Plan:</span> {{ $account->plan }}</p>
        <p><span class="text-gray-500">cPanel Username:</span> <span class="font-mono">{{ $account->cpanel_username }}</span></p>
        @if($account->server)
            <p><span class="text-gray-500">Server:</span> {{ $account->server }}</p>
        @endif
        @if($account->cpanel_url)
            <div class="pt-3">
                <a href="{{ route('hosting.cpanel', $account->id) }}" target="_blank"
                   class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 inline-block">
                    Open cPanel &rarr;
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
