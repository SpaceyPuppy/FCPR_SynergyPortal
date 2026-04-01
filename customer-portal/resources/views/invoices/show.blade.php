<x-app-layout :title="'Invoice #' . $invoice->id">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-lg shadow p-5 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-700">Invoice Details</h2>
            <p><span class="text-gray-500">Invoice #:</span> {{ $invoice->id }}</p>
            <p><span class="text-gray-500">Date:</span> {{ $invoice->created_at->format('d M Y') }}</p>
            <p><span class="text-gray-500">Due:</span> {{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
            <p><span class="text-gray-500">Status:</span> <x-badge :status="$invoice->status" /></p>
            @if($invoice->notes)
                <p><span class="text-gray-500">Notes:</span> {{ $invoice->notes }}</p>
            @endif

            {{-- Pay Now button — payment coming soon --}}
            @if(in_array($invoice->status, ['unpaid', 'overdue']))
                <div class="pt-3">
                    <button disabled
                            class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded text-sm cursor-not-allowed">
                        Pay Now — Coming Soon
                    </button>
                    <p class="text-xs text-gray-400 mt-1 text-center">
                        Online payments will be available soon. Please contact us to arrange payment.
                    </p>
                </div>
            @endif
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
                        <td class="px-4 py-2 text-right">${{ number_format($invoice->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right text-xs text-gray-500">GST (10%)</td>
                        <td class="px-4 py-2 text-right text-gray-500">${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-bold text-gray-800">Total (inc. GST)</td>
                        <td class="px-4 py-2 text-right font-bold">${{ number_format($invoice->amount + $invoice->tax_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</x-app-layout>
