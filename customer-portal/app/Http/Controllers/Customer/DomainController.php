<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\SynergyWholesaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function __construct(private SynergyWholesaleService $synergy) {}

    public function index(): View
    {
        return view('domains.index', [
            'domains' => auth()->user()->domains()->orderBy('expiry_date')->paginate(20),
        ]);
    }

    public function show(int $id): View
    {
        $domain  = auth()->user()->domains()->findOrFail($id);
        $apiInfo = $this->synergy->getDomainInfo($domain->domain_name);

        return view('domains.show', compact('domain', 'apiInfo'));
    }

    public function editNameservers(int $id): View
    {
        $domain = auth()->user()->domains()->findOrFail($id);

        return view('domains.edit-nameservers', compact('domain'));
    }

    public function updateNameservers(Request $request, int $id): RedirectResponse
    {
        $domain    = auth()->user()->domains()->findOrFail($id);
        $validated = $request->validate([
            'nameservers'   => 'required|array|min:2|max:6',
            'nameservers.*' => 'required|string|max:255',
        ]);

        if ($this->synergy->updateNameservers($domain->domain_name, $validated['nameservers'])) {
            $domain->update(['nameservers' => $validated['nameservers']]);

            return redirect()->route('domains.show', $id)->with('success', 'Nameservers updated.');
        }

        return back()->with('error', 'Failed to update nameservers. Please try again.');
    }

    public function toggleAutoRenew(Request $request, int $id): RedirectResponse
    {
        $domain = auth()->user()->domains()->findOrFail($id);
        $enable = (bool) $request->input('auto_renew');

        $success = $enable
            ? $this->synergy->enableAutoRenewal($domain->domain_name)
            : $this->synergy->disableAutoRenewal($domain->domain_name);

        if ($success) {
            $domain->update(['auto_renew' => $enable]);

            return back()->with('success', 'Auto-renew updated.');
        }

        return back()->with('error', 'Failed to update auto-renew setting.');
    }
}
