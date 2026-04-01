<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\User;
use App\Services\SynergyWholesaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function __construct(private SynergyWholesaleService $synergy) {}

    public function index(): View
    {
        $domains = Domain::with('user')
            ->orderBy('expiry_date')
            ->paginate(50);

        return view('domains.index', compact('domains'));
    }

    public function check(): View
    {
        return view('domains.check');
    }

    public function checkAvailability(Request $request): View
    {
        $validated = $request->validate(['domain_name' => 'required|string|max:255']);
        $available = $this->synergy->checkDomainAvailability($validated['domain_name']);

        return view('domains.check', array_merge($validated, compact('available')));
    }

    public function register(): View
    {
        $customers = User::where('is_admin', false)->orderBy('name')->get();

        return view('domains.register', compact('customers'));
    }

    public function storeDomain(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'        => 'required|exists:users,id',
            'domain_name'        => 'required|string|max:255',
            'years'              => 'required|integer|min:1|max:10',
            'nameservers'        => 'required|array|min:2|max:6',
            'nameservers.*'      => 'required|string|max:255',
            'id_protect'         => 'nullable|boolean',
            'contact.firstName'  => 'required|string|max:100',
            'contact.lastName'   => 'required|string|max:100',
            'contact.email'      => 'required|email',
            'contact.phone'      => 'required|string|max:30',
            'contact.address1'   => 'required|string|max:255',
            'contact.city'       => 'required|string|max:100',
            'contact.state'      => 'required|string|max:100',
            'contact.postcode'   => 'required|string|max:20',
            'contact.country'    => 'required|string|size:2',
        ]);

        $result = $this->synergy->registerDomain(
            $validated['domain_name'],
            (int) $validated['years'],
            $validated['nameservers'],
            (bool) ($validated['id_protect'] ?? false),
            $validated['contact']
        );

        if ($result) {
            $domain = Domain::create([
                'user_id'     => $validated['customer_id'],
                'domain_name' => $validated['domain_name'],
                'nameservers' => $validated['nameservers'],
                'expiry_date' => now()->addYears((int) $validated['years']),
                'auto_renew'  => true,
                'status'      => 'active',
            ]);

            return redirect()->route('domains.show', $domain->id)
                ->with('success', 'Domain registered successfully.');
        }

        return back()
            ->with('error', 'Domain registration failed. Check the API logs for details.')
            ->withInput();
    }

    public function show(int $id): View
    {
        $domain = Domain::with('user')->findOrFail($id);
        $apiInfo = $this->synergy->getDomainInfo($domain->domain_name);

        return view('domains.show', compact('domain', 'apiInfo'));
    }

    public function updateNameservers(Request $request, int $id): RedirectResponse
    {
        $domain = Domain::findOrFail($id);
        $validated = $request->validate([
            'nameservers'   => 'required|array|min:2|max:6',
            'nameservers.*' => 'required|string|max:255',
        ]);

        if ($this->synergy->updateNameservers($domain->domain_name, $validated['nameservers'])) {
            $domain->update(['nameservers' => $validated['nameservers']]);

            return back()->with('success', 'Nameservers updated.');
        }

        return back()->with('error', 'Failed to update nameservers.');
    }

    public function toggleAutoRenew(Request $request, int $id): RedirectResponse
    {
        $domain = Domain::findOrFail($id);
        $enable = (bool) $request->input('auto_renew');

        $success = $enable
            ? $this->synergy->enableAutoRenewal($domain->domain_name)
            : $this->synergy->disableAutoRenewal($domain->domain_name);

        if ($success) {
            $domain->update(['auto_renew' => $enable]);

            return back()->with('success', 'Auto-renew updated.');
        }

        return back()->with('error', 'Failed to update auto-renew.');
    }

    public function renew(Request $request, int $id): RedirectResponse
    {
        $domain = Domain::findOrFail($id);
        $years  = (int) $request->input('years', 1);

        if ($this->synergy->renewDomain($domain->domain_name, $years)) {
            $domain->update([
                'expiry_date' => $domain->expiry_date
                    ? $domain->expiry_date->addYears($years)
                    : now()->addYears($years),
            ]);

            return back()->with('success', "Domain renewed for {$years} year(s).");
        }

        return back()->with('error', 'Renewal failed. Check API logs.');
    }

    public function lock(int $id): RedirectResponse
    {
        $domain = Domain::findOrFail($id);
        $this->synergy->lockDomain($domain->domain_name)
            ? back()->with('success', 'Domain locked.')
            : back()->with('error', 'Lock failed.');

        return back();
    }

    public function unlock(int $id): RedirectResponse
    {
        $domain = Domain::findOrFail($id);
        $this->synergy->unlockDomain($domain->domain_name)
            ? back()->with('success', 'Domain unlocked.')
            : back()->with('error', 'Unlock failed.');

        return back();
    }
}
