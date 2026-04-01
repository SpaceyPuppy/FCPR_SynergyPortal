<x-app-layout title="Hosting Accounts">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($accounts as $account)
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="font-semibold text-gray-800">{{ $account->domain }}</h3>
                    <x-badge :status="$account->status" />
                </div>
                <p class="text-sm text-gray-500 mb-1">Plan: <span class="text-gray-700">{{ $account->plan }}</span></p>
                <p class="text-sm text-gray-500 mb-4">Username: <span class="text-gray-700 font-mono">{{ $account->cpanel_username }}</span></p>
                <div class="flex gap-2">
                    <a href="{{ route('hosting.show', $account->id) }}"
                       class="text-sm text-blue-600 hover:underline">Details</a>
                    @if($account->cpanel_url)
                        <a href="{{ route('hosting.cpanel', $account->id) }}" target="_blank"
                           class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            Open cPanel
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-lg shadow p-8 text-center text-gray-400">
                No hosting accounts yet. Contact us to get started.
            </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $accounts->links() }}</div>
</x-app-layout>
