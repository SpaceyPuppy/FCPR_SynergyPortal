<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\HostingAccount;
use App\Models\Invoice;
use App\Models\User;
use App\Services\SynergyWholesaleService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private SynergyWholesaleService $synergy) {}

    public function index(): View
    {
        return view('dashboard', [
            'customerCount'      => User::where('is_admin', false)->count(),
            'activeHostingCount' => HostingAccount::where('status', 'active')->count(),
            'domainCount'        => Domain::count(),
            'unpaidInvoiceCount' => Invoice::where('status', 'unpaid')->count(),
            'unpaidInvoiceTotal' => Invoice::where('status', 'unpaid')->sum('amount'),
            'expiringDomains'    => Domain::whereNotNull('expiry_date')
                                          ->whereDate('expiry_date', '<=', now()->addDays(30))
                                          ->orderBy('expiry_date')
                                          ->with('user')
                                          ->take(10)
                                          ->get(),
            'recentCustomers'    => User::where('is_admin', false)
                                        ->latest()
                                        ->take(5)
                                        ->get(),
            'apiBalance'         => $this->synergy->getBalance(),
        ]);
    }
}
