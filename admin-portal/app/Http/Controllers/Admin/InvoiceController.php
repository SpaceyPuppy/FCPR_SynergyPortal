<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with('user')->latest()->paginate(30);

        return view('invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        $customers = User::where('is_admin', false)->orderBy('name')->get();

        return view('invoices.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'               => 'required|exists:users,id',
            'due_date'              => 'required|date',
            'notes'                 => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.description'   => 'required|string|max:255',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        $subtotal = collect($validated['items'])
            ->sum(fn ($i) => $i['quantity'] * $i['unit_price']);

        $taxAmount = round($subtotal * 0.1, 2); // 10% GST

        $invoice = Invoice::create([
            'user_id'    => $validated['user_id'],
            'amount'     => $subtotal,
            'tax_amount' => $taxAmount,
            'status'     => 'unpaid',
            'due_date'   => $validated['due_date'],
            'notes'      => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'amount'      => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice created.');
    }

    public function show(int $id): View
    {
        $invoice = Invoice::with(['user', 'items'])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $invoice   = Invoice::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:draft,unpaid,paid,overdue,cancelled',
        ]);

        $data = $validated;
        if ($validated['status'] === 'paid' && ! $invoice->paid_at) {
            $data['paid_at'] = now();
        }

        $invoice->update($data);

        return back()->with('success', 'Invoice status updated.');
    }
}
