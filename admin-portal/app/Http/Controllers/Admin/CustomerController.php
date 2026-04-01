<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::where('is_admin', false)
            ->withCount(['domains', 'hostingAccounts', 'invoices'])
            ->latest()
            ->paginate(25);

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'company'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:30',
            'password' => 'required|min:8|confirmed',
        ]);

        $customer = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
        ]);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Customer created successfully.');
    }

    public function show(int $id): View
    {
        $customer = User::where('is_admin', false)
            ->with(['domains', 'hostingAccounts', 'sslCertificates', 'invoices'])
            ->findOrFail($id);

        return view('customers.show', compact('customer'));
    }

    public function edit(int $id): View
    {
        $customer = User::where('is_admin', false)->findOrFail($id);

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $customer = User::where('is_admin', false)->findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'email', Rule::unique('users')->ignore($id)],
            'company' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:30',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $id)
            ->with('success', 'Customer updated.');
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $customer = User::where('is_admin', false)->findOrFail($id);
        $customer->update(['is_active' => ! $customer->is_active]);

        $status = $customer->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Customer {$status}.");
    }
}
