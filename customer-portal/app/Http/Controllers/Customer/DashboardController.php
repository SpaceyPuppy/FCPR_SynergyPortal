<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('dashboard', [
            'hostingCount'    => $user->hostingAccounts()->where('status', 'active')->count(),
            'domainCount'     => $user->domains()->count(),
            'sslCount'        => $user->sslCertificates()->where('status', 'active')->count(),
            'recentInvoices'  => $user->invoices()->latest()->take(5)->with('items')->get(),
            'expiringDomains' => $user->domains()
                                      ->whereNotNull('expiry_date')
                                      ->whereDate('expiry_date', '<=', now()->addDays(30))
                                      ->orderBy('expiry_date')
                                      ->get(),
            'unpaidInvoices'  => $user->invoices()->where('status', 'unpaid')->count(),
        ]);
    }
}
