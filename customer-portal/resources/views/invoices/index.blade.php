<x-app-layout title="My Invoices">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Invoice</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Due Date</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Amount</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">#{{ $invoice->id }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($invoice->amount, 2) }}</td>
                        <td class="px-4 py-3 text-center"><x-badge :status="$invoice->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">No invoices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</x-app-layout>
