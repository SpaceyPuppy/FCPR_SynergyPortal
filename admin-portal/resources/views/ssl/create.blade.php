<x-app-layout title="New SSL Certificate">
    <div class="max-w-lg bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('ssl.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Customer *</label>
                <select name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('user_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Domain *</label>
                <input type="text" name="domain" value="{{ old('domain') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Type *</label>
                <select name="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    @foreach(['DV','OV','EV','Wildcard'] as $t)
                        <option value="{{ $t }}" {{ old('type', 'DV') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Synergy Reference</label>
                <input type="text" name="synergy_ref" value="{{ old('synergy_ref') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Create
                </button>
                <a href="{{ route('ssl.index') }}" class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
