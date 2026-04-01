<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SslCertificate;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SslController extends Controller
{
    public function index(): View
    {
        $certificates = SslCertificate::with('user')->orderBy('expiry_date')->paginate(30);

        return view('ssl.index', compact('certificates'));
    }

    public function create(): View
    {
        $customers = User::where('is_admin', false)->orderBy('name')->get();

        return view('ssl.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'domain'      => 'required|string|max:255',
            'type'        => 'required|in:DV,OV,EV,Wildcard',
            'expiry_date' => 'nullable|date',
            'synergy_ref' => 'nullable|string|max:100',
        ]);

        $cert = SslCertificate::create(array_merge($validated, ['status' => 'pending']));

        return redirect()->route('ssl.show', $cert->id)
            ->with('success', 'SSL certificate record created.');
    }

    public function show(int $id): View
    {
        $certificate = SslCertificate::with('user')->findOrFail($id);

        return view('ssl.show', compact('certificate'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $certificate = SslCertificate::findOrFail($id);
        $validated   = $request->validate([
            'status'      => 'required|in:active,expired,pending,revoked',
            'expiry_date' => 'nullable|date',
        ]);

        $certificate->update($validated);

        return back()->with('success', 'SSL certificate updated.');
    }
}
