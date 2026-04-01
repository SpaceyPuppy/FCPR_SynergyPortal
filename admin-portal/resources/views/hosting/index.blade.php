<x-app-layout title="Hosting Accounts">
    <div class="flex justify-end mb-4">
        <a href="{{ route('hosting.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            + New Account
        </a>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Domain</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Plan</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">cPanel User</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($accounts as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $account->domain }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            <a href="{{ route('customers.show', $account->user_id) }}" class="hover:underline">
                                {{ $account->user->name ?? '—' }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $account->plan }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $account->cpanel_username }}</td>
                        <td class="px-4 py-3 text-center"><x-badge :status="$account->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('hosting.show', $account->id) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No hosting accounts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $accounts->links() }}</div>
</x-app-layout>
