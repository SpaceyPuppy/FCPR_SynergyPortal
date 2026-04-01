<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HostingController extends Controller
{
    public function index(): View
    {
        return view('hosting.index', [
            'accounts' => auth()->user()->hostingAccounts()->paginate(20),
        ]);
    }

    public function show(int $id): View
    {
        $account = auth()->user()->hostingAccounts()->findOrFail($id);

        return view('hosting.show', compact('account'));
    }

    public function cPanelLogin(int $id): RedirectResponse
    {
        $account = auth()->user()->hostingAccounts()->findOrFail($id);

        if ($account->cpanel_url) {
            return redirect()->away($account->cpanel_url);
        }

        return back()->with('error', 'cPanel URL not configured for this account.');
    }
}
