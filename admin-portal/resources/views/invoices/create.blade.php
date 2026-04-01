<x-app-layout title="New Invoice">
    <div class="max-w-2xl bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('invoices.store') }}" class="space-y-5" id="invoice-form">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer *</label>
                    <select name="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">— Select customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('user_id', request('customer_id')) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date *</label>
                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Line Items *</label>
                <div id="items" class="space-y-2">
                    <div class="flex gap-2 items-center line-item">
                        <input type="text" name="items[0][description]" placeholder="Description" required
                               class="flex-1 border-gray-300 rounded text-sm">
                        <input type="number" name="items[0][quantity]" value="1" min="1" required
                               class="w-16 border-gray-300 rounded text-sm text-center">
                        <input type="number" name="items[0][unit_price]" placeholder="Price" step="0.01" min="0" required
                               class="w-28 border-gray-300 rounded text-sm">
                    </div>
                </div>
                <button type="button" id="add-item"
                        class="mt-2 text-sm text-blue-600 hover:underline">
                    + Add line item
                </button>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Create Invoice
                </button>
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
    let idx = 1;
    document.getElementById('add-item').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center line-item';
        div.innerHTML = `
            <input type="text" name="items[${idx}][description]" placeholder="Description" required class="flex-1 border-gray-300 rounded text-sm">
            <input type="number" name="items[${idx}][quantity]" value="1" min="1" required class="w-16 border-gray-300 rounded text-sm text-center">
            <input type="number" name="items[${idx}][unit_price]" placeholder="Price" step="0.01" min="0" required class="w-28 border-gray-300 rounded text-sm">
            <button type="button" class="text-red-500 text-sm hover:underline remove-item">Remove</button>
        `;
        div.querySelector('.remove-item').addEventListener('click', () => div.remove());
        document.getElementById('items').appendChild(div);
        idx++;
    });
    </script>
</x-app-layout>
