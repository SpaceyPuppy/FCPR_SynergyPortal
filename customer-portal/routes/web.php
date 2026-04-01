<?php

use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\DomainController;
use App\Http\Controllers\Customer\HostingController;
use App\Http\Controllers\Customer\SslController;
use App\Http\Controllers\Customer\DnsController;
use App\Http\Controllers\Customer\InvoiceController;
use App\Http\Controllers\Customer\AccountController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Hosting
    Route::prefix('hosting')->name('hosting.')->group(function () {
        Route::get('/', [HostingController::class, 'index'])->name('index');
        Route::get('/{id}', [HostingController::class, 'show'])->name('show');
        Route::get('/{id}/cpanel', [HostingController::class, 'cPanelLogin'])->name('cpanel');
    });

    // Domains
    Route::prefix('domains')->name('domains.')->group(function () {
        Route::get('/', [DomainController::class, 'index'])->name('index');
        Route::get('/{id}', [DomainController::class, 'show'])->name('show');
        Route::get('/{id}/edit-nameservers', [DomainController::class, 'editNameservers'])->name('edit-nameservers');
        Route::patch('/{id}/nameservers', [DomainController::class, 'updateNameservers'])->name('update-nameservers');
        Route::patch('/{id}/auto-renew', [DomainController::class, 'toggleAutoRenew'])->name('toggle-auto-renew');
    });

    // SSL
    Route::prefix('ssl')->name('ssl.')->group(function () {
        Route::get('/', [SslController::class, 'index'])->name('index');
        Route::get('/{id}', [SslController::class, 'show'])->name('show');
    });

    // DNS
    Route::prefix('dns')->name('dns.')->group(function () {
        Route::get('/', [DnsController::class, 'index'])->name('index');
        Route::get('/{domain}', [DnsController::class, 'show'])->name('show');
        Route::get('/{domain}/create', [DnsController::class, 'create'])->name('create');
        Route::post('/{domain}', [DnsController::class, 'store'])->name('store');
        Route::delete('/{domain}/{id}', [DnsController::class, 'destroy'])->name('destroy');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
    });

    // Account
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'show'])->name('show');
        Route::patch('/profile', [AccountController::class, 'updateProfile'])->name('update-profile');
        Route::patch('/password', [AccountController::class, 'updatePassword'])->name('update-password');
    });
});
