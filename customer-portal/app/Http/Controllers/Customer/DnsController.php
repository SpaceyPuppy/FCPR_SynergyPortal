<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DnsRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DnsController extends Controller
{
    public function index(): View
    {
        return view('dns.index', [
            'domains' => auth()->user()->domains()->get(),
        ]);
    }

    public function show(string $domainName): View
    {
        $domain  = auth()->user()->domains()->where('domain_name', $domainName)->firstOrFail();
        $records = DnsRecord::where('user_id', auth()->id())
                             ->where('domain_name', $domainName)
                             ->orderBy('type')
                             ->get();

        return view('dns.show', compact('domain', 'records'));
    }

    public function create(string $domainName): View
    {
        $domain = auth()->user()->domains()->where('domain_name', $domainName)->firstOrFail();

        return view('dns.create', compact('domain'));
    }

    public function store(Request $request, string $domainName): RedirectResponse
    {
        auth()->user()->domains()->where('domain_name', $domainName)->firstOrFail();

        $validated = $request->validate([
            'type'     => 'required|in:A,AAAA,CNAME,MX,TXT,NS,SRV',
            'host'     => 'required|string|max:255',
            'content'  => 'required|string|max:1024',
            'ttl'      => 'nullable|integer|min:60|max:86400',
            'priority' => 'nullable|integer|min:0|max:65535',
        ]);

        DnsRecord::create(array_merge($validated, [
            'user_id'     => auth()->id(),
            'domain_name' => $domainName,
            'ttl'         => $validated['ttl'] ?? 3600,
        ]));

        return redirect()->route('dns.show', $domainName)->with('success', 'DNS record added.');
    }

    public function destroy(string $domainName, int $id): RedirectResponse
    {
        DnsRecord::where('user_id', auth()->id())
                  ->where('domain_name', $domainName)
                  ->findOrFail($id)
                  ->delete();

        return redirect()->route('dns.show', $domainName)->with('success', 'DNS record deleted.');
    }
}
