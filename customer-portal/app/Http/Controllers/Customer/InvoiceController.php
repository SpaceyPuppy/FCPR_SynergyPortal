<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return view('invoices.index', [
            'invoices' => auth()->user()->invoices()->latest()->paginate(20),
        ]);
    }

    public function show(int $id): View
    {
        $invoice = auth()->user()->invoices()->with('items')->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }
}
