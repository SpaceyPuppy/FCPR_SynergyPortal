<x-app-layout title="Synergy Wholesale API">
    <div class="max-w-lg space-y-6">

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-4">API Connection Status</h2>
            @if($connected)
                <div class="flex items-center gap-2 text-sm text-green-700 mb-3">
                    <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
                    <strong>Connected</strong>
                </div>
                <p class="text-sm text-gray-600">Account Balance:
                    <span class="font-semibold text-gray-900">${{ number_format($balance, 2) }}</span>
                </p>
            @else
                <div class="flex items-center gap-2 text-sm text-red-600 mb-3">
                    <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
                    <strong>Not Connected</strong>
                </div>
                <p class="text-sm text-gray-500">
                    Check that <code class="bg-gray-100 px-1 rounded">SYNERGY_RESELLER_ID</code> and
                    <code class="bg-gray-100 px-1 rounded">SYNERGY_API_KEY</code> are set in your
                    <code class="bg-gray-100 px-1 rounded">.env</code> file, and that your server IP
                    is whitelisted in the Synergy Wholesale portal.
                </p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-2">Sync Domains</h2>
            <p class="text-sm text-gray-500 mb-4">
                Fetch all domains from Synergy Wholesale and update expiry dates and statuses
                for any domains already in the local database.
            </p>
            <form method="POST" action="{{ route('synergy.sync-domains') }}">
                @csrf
                <button @if(!$connected) disabled @endif
                        class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Sync Domains from Synergy
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
