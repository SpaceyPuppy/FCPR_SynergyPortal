<x-app-layout :title="'Invoice #' . $invoice->id">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-700">Invoice Details</h2>
            <p><span class="text-gray-500">Customer:</span>
                <a href="{{ route('customers.show', $invoice->user_id) }}" class="text-blue-600 hover:underline">
                    {{ $invoice->user->name }}
                </a>
            </p>
            <p><span class="text-gray-500">Due:</span> {{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
            <p><span class="text-gray-500">Status:</span> <x-badge :status="$invoice->status" /></p>
            @if($invoice->paid_at)
                <p><span class="text-gray-500">Paid:</span> {{ $invoice->paid_at->format('d M Y') }}</p>
            @endif
            @if($invoice->notes)
                <p><span class="text-gray-500">Notes:</span> {{ $invoice->notes }}</p>
            @endif

            <form method="POST" action="{{ route('invoices.update-status', $invoice->id) }}" class="flex gap-2 pt-3">
                @csrf @method('PATCH')
                <select name="status" class="border-gray-300 rounded text-sm">
                    @foreach(['draft','unpaid','paid','overdue','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $invoice->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="bg-gray-700 text-white px-3 py-1.5 rounded text-sm hover:bg-gray-800">Update</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow lg:col-span-2">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-700">Line Items</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Description</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-500">Qty</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-500">Unit Price</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-500">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->description }}</td>
                            <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-2 text-right">${{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-medium text-gray-600">Subtotal</td>
                        <td class="px-4 py-2 text-right font-medium">${{ number_format($invoice->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right text-gray-500 text-xs">GST (10%)</td>
                        <td class="px-4 py-2 text-right text-gray-500">${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-bold text-gray-800">Total</td>
                        <td class="px-4 py-2 text-right font-bold">${{ number_format($invoice->amount + $invoice->tax_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</x-app-layout>
