<x-app-layout title="New Hosting Account">
    <div class="max-w-lg bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('hosting.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Customer *</label>
                <select name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('user_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Domain *</label>
                <input type="text" name="domain" value="{{ old('domain') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Plan *</label>
                <input type="text" name="plan" value="{{ old('plan') }}" required
                       placeholder="e.g. Starter, Business"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">cPanel Username *</label>
                <input type="text" name="cpanel_username" value="{{ old('cpanel_username') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">cPanel URL</label>
                <input type="url" name="cpanel_url" value="{{ old('cpanel_url') }}"
                       placeholder="https://server.example.com:2083"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Server Hostname</label>
                <input type="text" name="server" value="{{ old('server') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Synergy Reference</label>
                <input type="text" name="synergy_ref" value="{{ old('synergy_ref') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Create Account
                </button>
                <a href="{{ route('hosting.index') }}" class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
