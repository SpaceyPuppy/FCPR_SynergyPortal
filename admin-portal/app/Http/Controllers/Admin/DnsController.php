<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DnsRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DnsController extends Controller
{
    public function index(): View
    {
        $domains = Domain::with('user')->orderBy('domain_name')->paginate(50);

        return view('dns.index', compact('domains'));
    }

    public function show(string $domain): View
    {
        $domainModel = Domain::where('domain_name', $domain)->with('user')->firstOrFail();
        $records     = DnsRecord::where('domain_name', $domain)->orderBy('type')->get();

        return view('dns.show', compact('domainModel', 'records'));
    }

    public function store(Request $request, string $domain): RedirectResponse
    {
        Domain::where('domain_name', $domain)->firstOrFail();

        $domainModel = Domain::where('domain_name', $domain)->firstOrFail();

        $validated = $request->validate([
            'type'     => 'required|in:A,AAAA,CNAME,MX,TXT,NS,SRV',
            'host'     => 'required|string|max:255',
            'content'  => 'required|string|max:1024',
            'ttl'      => 'nullable|integer|min:60|max:86400',
            'priority' => 'nullable|integer|min:0|max:65535',
        ]);

        DnsRecord::create(array_merge($validated, [
            'user_id'     => $domainModel->user_id,
            'domain_name' => $domain,
            'ttl'         => $validated['ttl'] ?? 3600,
        ]));

        return redirect()->route('dns.show', $domain)->with('success', 'DNS record added.');
    }

    public function update(Request $request, string $domain, int $id): RedirectResponse
    {
        $record    = DnsRecord::where('domain_name', $domain)->findOrFail($id);
        $validated = $request->validate([
            'content'  => 'required|string|max:1024',
            'ttl'      => 'nullable|integer|min:60',
            'priority' => 'nullable|integer|min:0',
        ]);

        $record->update($validated);

        return redirect()->route('dns.show', $domain)->with('success', 'DNS record updated.');
    }

    public function destroy(string $domain, int $id): RedirectResponse
    {
        DnsRecord::where('domain_name', $domain)->findOrFail($id)->delete();

        return redirect()->route('dns.show', $domain)->with('success', 'DNS record deleted.');
    }
}
