<x-app-layout :title="'Edit: ' . $account->domain">
    <div class="max-w-lg bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('hosting.update', $account->id) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700">Domain *</label>
                <input type="text" name="domain" value="{{ old('domain', $account->domain) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Plan *</label>
                <input type="text" name="plan" value="{{ old('plan', $account->plan) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">cPanel URL</label>
                <input type="url" name="cpanel_url" value="{{ old('cpanel_url', $account->cpanel_url) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Server</label>
                <input type="text" name="server" value="{{ old('server', $account->server) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Save Changes
                </button>
                <a href="{{ route('hosting.show', $account->id) }}"
                   class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
