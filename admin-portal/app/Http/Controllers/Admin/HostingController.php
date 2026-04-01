<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HostingController extends Controller
{
    public function index(): View
    {
        $accounts = HostingAccount::with('user')->latest()->paginate(30);

        return view('hosting.index', compact('accounts'));
    }

    public function create(): View
    {
        $customers = User::where('is_admin', false)->orderBy('name')->get();

        return view('hosting.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'domain'          => 'required|string|max:255',
            'plan'            => 'required|string|max:100',
            'cpanel_username' => 'required|string|max:50|unique:hosting_accounts,cpanel_username',
            'cpanel_url'      => 'nullable|url|max:255',
            'server'          => 'nullable|string|max:255',
            'synergy_ref'     => 'nullable|string|max:100',
        ]);

        $account = HostingAccount::create(array_merge($validated, ['status' => 'active']));

        return redirect()->route('hosting.show', $account->id)
            ->with('success', 'Hosting account created.');
    }

    public function show(int $id): View
    {
        $account = HostingAccount::with('user')->findOrFail($id);

        return view('hosting.show', compact('account'));
    }

    public function edit(int $id): View
    {
        $account   = HostingAccount::findOrFail($id);
        $customers = User::where('is_admin', false)->orderBy('name')->get();

        return view('hosting.edit', compact('account', 'customers'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $account   = HostingAccount::findOrFail($id);
        $validated = $request->validate([
            'domain'     => 'required|string|max:255',
            'plan'       => 'required|string|max:100',
            'cpanel_url' => 'nullable|url|max:255',
            'server'     => 'nullable|string|max:255',
        ]);

        $account->update($validated);

        return redirect()->route('hosting.show', $id)->with('success', 'Account updated.');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $account   = HostingAccount::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:active,suspended,terminated,pending',
        ]);

        $account->update($validated);

        return back()->with('success', 'Status updated.');
    }
}
