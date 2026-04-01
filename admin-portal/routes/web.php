<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\HostingController;
use App\Http\Controllers\Admin\SslController;
use App\Http\Controllers\Admin\DnsController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\SynergyController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-active', [CustomerController::class, 'toggleActive'])->name('toggle-active');
    });

    // Domains
    Route::prefix('domains')->name('domains.')->group(function () {
        Route::get('/', [DomainController::class, 'index'])->name('index');
        Route::get('/check', [DomainController::class, 'check'])->name('check');
        Route::post('/check', [DomainController::class, 'checkAvailability'])->name('check-availability');
        Route::get('/register', [DomainController::class, 'register'])->name('register');
        Route::post('/register', [DomainController::class, 'storeDomain'])->name('store');
        Route::get('/{id}', [DomainController::class, 'show'])->name('show');
        Route::patch('/{id}/nameservers', [DomainController::class, 'updateNameservers'])->name('update-nameservers');
        Route::patch('/{id}/auto-renew', [DomainController::class, 'toggleAutoRenew'])->name('toggle-auto-renew');
        Route::post('/{id}/renew', [DomainController::class, 'renew'])->name('renew');
        Route::post('/{id}/lock', [DomainController::class, 'lock'])->name('lock');
        Route::post('/{id}/unlock', [DomainController::class, 'unlock'])->name('unlock');
    });

    // Hosting
    Route::prefix('hosting')->name('hosting.')->group(function () {
        Route::get('/', [HostingController::class, 'index'])->name('index');
        Route::get('/create', [HostingController::class, 'create'])->name('create');
        Route::post('/', [HostingController::class, 'store'])->name('store');
        Route::get('/{id}', [HostingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [HostingController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [HostingController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [HostingController::class, 'updateStatus'])->name('update-status');
    });

    // SSL
    Route::prefix('ssl')->name('ssl.')->group(function () {
        Route::get('/', [SslController::class, 'index'])->name('index');
        Route::get('/create', [SslController::class, 'create'])->name('create');
        Route::post('/', [SslController::class, 'store'])->name('store');
        Route::get('/{id}', [SslController::class, 'show'])->name('show');
        Route::patch('/{id}', [SslController::class, 'update'])->name('update');
    });

    // DNS
    Route::prefix('dns')->name('dns.')->group(function () {
        Route::get('/', [DnsController::class, 'index'])->name('index');
        Route::get('/{domain}', [DnsController::class, 'show'])->name('show');
        Route::post('/{domain}', [DnsController::class, 'store'])->name('store');
        Route::patch('/{domain}/{id}', [DnsController::class, 'update'])->name('update');
        Route::delete('/{domain}/{id}', [DnsController::class, 'destroy'])->name('destroy');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
    });

    // Synergy Wholesale API status
    Route::prefix('synergy')->name('synergy.')->group(function () {
        Route::get('/', [SynergyController::class, 'index'])->name('index');
        Route::post('/sync-domains', [SynergyController::class, 'syncDomains'])->name('sync-domains');
    });
});
