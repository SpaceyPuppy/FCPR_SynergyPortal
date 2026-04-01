<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SslController extends Controller
{
    public function index(): View
    {
        return view('ssl.index', [
            'certificates' => auth()->user()->sslCertificates()->orderBy('expiry_date')->paginate(20),
        ]);
    }

    public function show(int $id): View
    {
        $certificate = auth()->user()->sslCertificates()->findOrFail($id);

        return view('ssl.show', compact('certificate'));
    }
}
