<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Services\SynergyWholesaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SynergyController extends Controller
{
    public function __construct(private SynergyWholesaleService $synergy) {}

    public function index(): View
    {
        $connected = $this->synergy->testConnection();
        $balance   = $connected ? $this->synergy->getBalance() : null;

        return view('synergy.index', compact('connected', 'balance'));
    }

    public function syncDomains(): RedirectResponse
    {
        $result = $this->synergy->listDomains(1, 1000);

        if (! $result) {
            return back()->with('error', 'Failed to fetch domains from Synergy Wholesale API.');
        }

        $synced = 0;
        foreach ((array) $result['domains'] as $item) {
            $domainName = $item->domainName ?? $item->domain ?? null;
            if (! $domainName) {
                continue;
            }

            $local = Domain::where('domain_name', $domainName)->first();
            if ($local) {
                $local->update([
                    'expiry_date' => ! empty($item->domain_expiry)
                        ? \Carbon\Carbon::parse($item->domain_expiry)
                        : $local->expiry_date,
                    'status' => strtolower($item->domain_status ?? $local->status),
                ]);
                $synced++;
            }
        }

        return back()->with('success', "Synced {$synced} domain(s) from Synergy Wholesale.");
    }
}
