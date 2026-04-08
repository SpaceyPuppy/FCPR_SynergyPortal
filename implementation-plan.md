# Plan: Rewrite DNS and Hosting Modules — Full Synergy Wholesale API Compliance

## Context

The FCPR_SynergyPortal has two Laravel applications (`admin-portal`, `customer-portal`) that share a MySQL database and integrate with the Synergy Wholesale SOAP API via the `hampel/synergy-wholesale ^1.5` package. A full compliance audit (`compliance.md`) identified that the DNS and Hosting modules write exclusively to the local database and make **zero API calls** — meaning actions taken in the portal have no effect on live DNS resolution or actual hosting provisioning.

This plan covers a complete rewrite of both modules to be fully API-backed, addressing Issues 1, 2, 7, 8, and 9 from the compliance audit. Related quick-wins (Issues 4, 10, 13) in the same files are fixed as part of the same pass.

---

## Critical Pre-Condition: Package Gap

The `hampel/synergy-wholesale` package **does not implement DNS or Hosting commands** (the README explicitly states "Only Domain Name and SMS API calls have been implemented"). Custom Command and Response classes must be created inside the app namespace, following the package's Command/Response pattern so they work with the existing `$this->api->execute()` call.

The convention: `execute(new FooBarCommand(...))` → strips "Command" → calls SOAP method `FooBar` → instantiates `FooBarResponse`. We create our command classes in `App\Services\Synergy\Commands\` and response classes in `App\Services\Synergy\Responses\` to satisfy the ResponseGenerator's namespace-replacement convention.

Each Command must implement `SynergyWholesale\Commands\Command` (two methods: `getRequestData(): array`, `getKey(): string|null`).
Each Response must extend `SynergyWholesale\Responses\Response` (define `$expectedFields`, `$successStatus`, getter methods).

Since both portals are independent Laravel apps with no shared vendor path, these classes must exist in **both** portals.

---

## Files Modified / Created

### Both portals (identical changes)

| File | Action |
|---|---|
| `{portal}/app/Services/SynergyWholesaleService.php` | Add 10 new methods, remove 3 dead imports |
| `{portal}/app/Services/Synergy/Commands/AddDNSZoneCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/AddDNSRecordCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/UpdateDNSRecordCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/DeleteDNSRecordCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/ListDNSZoneCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/HostingPurchaseServiceCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/HostingSuspendServiceCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/HostingUnsuspendServiceCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/HostingGetLoginCommand.php` | Create |
| `{portal}/app/Services/Synergy/Commands/HostingListPackagesCommand.php` | Create |
| `{portal}/app/Services/Synergy/Responses/AddDNSZoneResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/AddDNSRecordResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/UpdateDNSRecordResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/DeleteDNSRecordResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/ListDNSZoneResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/HostingPurchaseServiceResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/HostingSuspendServiceResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/HostingUnsuspendServiceResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/HostingGetLoginResponse.php` | Create |
| `{portal}/app/Services/Synergy/Responses/HostingListPackagesResponse.php` | Create |

### Admin portal only

| File | Action |
|---|---|
| `admin-portal/app/Http/Controllers/Admin/DnsController.php` | Rewrite |
| `admin-portal/app/Http/Controllers/Admin/HostingController.php` | Rewrite |
| `admin-portal/app/Http/Controllers/Admin/SynergyController.php` | Fix pagination + field mapping (Issues 4, 13) |

### Customer portal only

| File | Action |
|---|---|
| `customer-portal/app/Http/Controllers/Customer/DnsController.php` | Rewrite |
| `customer-portal/app/Http/Controllers/Customer/HostingController.php` | Rewrite |
| `customer-portal/database/migrations/XXXX_add_synergy_record_id_to_dns_records_table.php` | Create |
| `customer-portal/app/Models/DnsRecord.php` | Add `synergy_record_id` to `$fillable` |

### Views (both portals, minor)

| File | Action |
|---|---|
| `{portal}/resources/views/dns/create.blade.php` | Remove NS, SRV from type `<select>` |
| `{portal}/resources/views/dns/show.blade.php` | Remove NS, SRV from type `<select>` |
| `admin-portal/resources/views/hosting/create.blade.php` | Replace plan text input with `<select>` from API |

---

## Step-by-Step Implementation

### Step 1 — Add `synergy_record_id` column (migration)

Create migration in `customer-portal/database/migrations/`:
```php
Schema::table('dns_records', function (Blueprint $table) {
    $table->unsignedBigInteger('synergy_record_id')->nullable()->after('priority');
});
```
Add `'synergy_record_id'` to `DnsRecord::$fillable`.

This column stores the record ID returned by Synergy's `addDNSRecord` API, required for future update/delete calls.

---

### Step 2 — Create DNS Command/Response classes (both portals)

**Pattern for all Command classes** (implement `SynergyWholesale\Commands\Command`):

```php
// App\Services\Synergy\Commands\AddDNSRecordCommand
public function getRequestData(): array {
    return [
        'domainName'    => $this->domain,
        'recordName'    => $this->recordName,   // maps from local 'host'
        'recordType'    => $this->recordType,   // maps from local 'type'
        'recordContent' => $this->recordContent, // maps from local 'content'
        'recordTTL'     => $this->recordTTL,    // maps from local 'ttl'
        'recordPrio'    => $this->recordPrio,   // maps from local 'priority' (MX only)
    ];
}
public function getKey(): ?string { return null; } // writes are not cacheable
```

DNS Commands and their SOAP equivalents (class name strips "Command"):
- `AddDNSZoneCommand` → SOAP `AddDNSZone` — params: `domainName`
- `AddDNSRecordCommand` → SOAP `AddDNSRecord` — params: `domainName`, `recordName`, `recordType`, `recordContent`, `recordTTL`, `recordPrio`
- `UpdateDNSRecordCommand` → SOAP `UpdateDNSRecord` — params: `domainName`, `recordId`, `recordContent`, `recordTTL`, `recordPrio`
- `DeleteDNSRecordCommand` → SOAP `DeleteDNSRecord` — params: `domainName`, `recordId`
- `ListDNSZoneCommand` → SOAP `ListDNSZone` — params: `domainName`

**Pattern for Response classes** (extend `SynergyWholesale\Responses\Response`):
```php
// App\Services\Synergy\Responses\AddDNSRecordResponse
protected $successStatus = ['OK'];
protected $expectedFields = ['recordId'];
public function getRecordId(): int { return (int) $this->response->recordId; }
```

`AddDNSZoneResponse` — `$expectedFields = []`, `$successStatus = ['OK']`
`AddDNSRecordResponse` — `$expectedFields = ['recordId']`, getter `getRecordId(): int`
`UpdateDNSRecordResponse` — `$expectedFields = []`, `$successStatus = ['OK']`
`DeleteDNSRecordResponse` — `$expectedFields = []`, `$successStatus = ['OK']`
`ListDNSZoneResponse` — `$expectedFields = ['records']`, getter `getRecords(): array`

---

### Step 3 — Create Hosting Command/Response classes (both portals)

Hosting Commands:
- `HostingPurchaseServiceCommand` → SOAP `HostingPurchaseService` — params: `packageName`, `domainName`, `username` (optional), `password` (optional), `email`
- `HostingSuspendServiceCommand` → SOAP `HostingSuspendService` — params: `hoid` (Synergy hosting reference)
- `HostingUnsuspendServiceCommand` → SOAP `HostingUnsuspendService` — params: `hoid`
- `HostingGetLoginCommand` → SOAP `HostingGetLogin` — params: `hoid`
- `HostingListPackagesCommand` → SOAP `HostingListPackages` — no params

Responses:
- `HostingPurchaseServiceResponse` — `$expectedFields = ['hoid', 'username', 'domain']`, getters for `getHoid()`, `getUsername()`, `getDomain()`
- `HostingSuspendServiceResponse` — `$expectedFields = []`, `$successStatus = ['OK']`
- `HostingUnsuspendServiceResponse` — `$expectedFields = []`, `$successStatus = ['OK']`
- `HostingGetLoginResponse` — `$expectedFields = ['url']`, getter `getUrl(): string`
- `HostingListPackagesResponse` — `$expectedFields = ['packages']`, getter `getPackages(): array`

---

### Step 4 — Extend SynergyWholesaleService (both portals)

Remove dead imports (Issue 10):
```php
// DELETE these three lines:
use SynergyWholesale\Commands\DomainRegisterAUCommand;
use SynergyWholesale\Types\AuContact;
use SynergyWholesale\Types\AuRegistrant;
```

Add DNS methods:
```php
public function addDnsZone(string $domain): bool
public function addDnsRecord(string $domain, string $host, string $type, string $content, int $ttl, ?int $priority): mixed // returns response with recordId
public function updateDnsRecord(string $domain, int $recordId, string $content, int $ttl, ?int $priority): bool
public function deleteDnsRecord(string $domain, int $recordId): bool
public function listDnsZone(string $domain): array
```

Add Hosting methods:
```php
public function purchaseHosting(string $planName, string $domain, string $email, ?string $username = null, ?string $password = null): mixed
public function suspendHosting(string $hoid): bool
public function unsuspendHosting(string $hoid): bool
public function getHostingLoginUrl(string $hoid): ?string
public function listHostingPackages(): array
```

All methods follow the existing pattern: `try { $this->api->execute(new FooCommand(...)); } catch (Exception $e) { Log::error(...); return null/false; }`

---

### Step 5 — Rewrite DnsController (admin portal)

Inject `SynergyWholesaleService` via constructor.

**`store()`** — API-first flow:
1. Validate: remove `NS`, `SRV` from `in:` rule (Issue 8) → `'type' => 'required|in:A,AAAA,CNAME,MX,TXT'`
2. Call `$this->synergy->addDnsRecord($domain, $validated['host'], $validated['type'], $validated['content'], $ttl, $priority)`
3. If API call fails → return error redirect, do NOT write to DB
4. On success → `DnsRecord::create([..., 'synergy_record_id' => $response->getRecordId()])`

**`update()`** — API-first flow:
1. Lookup `$record->synergy_record_id`; if null, return error (record not API-synced)
2. Call `$this->synergy->updateDnsRecord($domain, $record->synergy_record_id, $content, $ttl, $priority)`
3. On success → `$record->update($validated)`

**`destroy()`** — API-first flow:
1. Lookup `$record->synergy_record_id`; if null, log warning and proceed with local delete only
2. Call `$this->synergy->deleteDnsRecord($domain, $record->synergy_record_id)`
3. On success (or if no synergy_record_id) → `$record->delete()`

**`show()`** — optionally pull live from API:
- Keep current local-DB read for now; add `// TODO: optionally call listDnsZone() here` comment

---

### Step 6 — Rewrite DnsController (customer portal)

Same as admin but:
- No `update()` method (customer portal doesn't have one currently, leave it absent)
- `store()` and `destroy()` follow the same API-first flow
- Scope queries to `auth()->user()` (already in place)

---

### Step 7 — Rewrite HostingController (admin portal)

**`create()`** — load packages for dropdown:
```php
$packages = $this->synergy->listHostingPackages();
return view('hosting.create', compact('customers', 'packages'));
```

**`store()`** — provision via API:
1. Remove `cpanel_username` and `synergy_ref` from validation (these come from API)
2. Validate: `user_id`, `domain`, `plan` (value from `hostingListPackages` dropdown), `email`
3. Call `$this->synergy->purchaseHosting($validated['plan'], $validated['domain'], $validated['email'])`
4. If API fails → return error, do NOT create DB record
5. On success: `HostingAccount::create(['cpanel_username' => $response->getUsername(), 'synergy_ref' => $response->getHoid(), 'domain' => ..., 'plan' => ..., 'status' => 'active', 'user_id' => ...])`

**`updateStatus()`** — wire to API:
```php
if ($validated['status'] === 'suspended') {
    $ok = $this->synergy->suspendHosting($account->synergy_ref);
} elseif ($validated['status'] === 'active' && $account->status === 'suspended') {
    $ok = $this->synergy->unsuspendHosting($account->synergy_ref);
}
if (isset($ok) && !$ok) {
    return back()->with('error', 'API call failed. Status not changed.');
}
$account->update($validated);
```

---

### Step 8 — Rewrite HostingController (customer portal)

**`cPanelLogin()`** — replace static URL redirect with API-generated SSO link (Issue 7):
```php
public function cPanelLogin(int $id): RedirectResponse
{
    $account  = auth()->user()->hostingAccounts()->findOrFail($id);
    $loginUrl = $this->synergy->getHostingLoginUrl($account->synergy_ref);

    if ($loginUrl) {
        return redirect()->away($loginUrl);
    }

    return back()->with('error', 'Could not generate login link. Please try again.');
}
```
Inject `SynergyWholesaleService` via constructor.

---

### Step 9 — Fix SynergyController (admin portal, Issues 4 + 13)

**Issue 4** — Replace `listDomains(1, 1000)` with pagination loop (max 500 per page):
```php
$page = 1; $all = [];
do {
    $result = $this->synergy->listDomains($page, 500);
    if (!$result) break;
    $batch = (array) ($result['domains'] ?? []);
    $all   = array_merge($all, $batch);
    $page++;
} while (count($batch) === 500);
```

**Issue 13** — Fix fragile field access. Keep `$item->domainName` as primary, remove the `$item->domain` fallback, add a log warning when null:
```php
$domainName = $item->domainName ?? null;
if (!$domainName) {
    Log::warning('syncDomains: item missing domainName', ['item' => $item]);
    continue;
}
```

---

### Step 10 — Update views

In `{portal}/resources/views/dns/create.blade.php` and `dns/show.blade.php`:
- Remove `<option value="NS">NS</option>` and `<option value="SRV">SRV</option>`

In `admin-portal/resources/views/hosting/create.blade.php`:
- Replace plan text input with:
```blade
<select name="plan">
    @foreach($packages as $pkg)
        <option value="{{ $pkg->name ?? $pkg }}">{{ $pkg->name ?? $pkg }}</option>
    @endforeach
</select>
```
- Remove `cpanel_username`, `synergy_ref`, `cpanel_url` fields (populated from API)
- Add `email` field (required by `hostingPurchaseService`)

---

## Verification

1. **Unit smoke test** — after `composer install`, run `php artisan test` in each portal; confirm no class-not-found errors from removed imports
2. **DNS create** — create an A record via admin portal; verify no DB row is created when API key is wrong (error path); verify DB row + `synergy_record_id` populated on success
3. **DNS delete** — delete the record; verify `deleteDNSRecord` is called and local row is removed
4. **Hosting provision** — create hosting account in admin; verify `synergy_ref` and `cpanel_username` are auto-populated from API response (not entered manually)
5. **Hosting suspend/unsuspend** — change status in admin; verify API is called before local update
6. **cPanel login** — click login button in customer portal; verify redirect goes to fresh SSO URL (not a static stored string)
7. **syncDomains** — run sync in admin; verify it pages correctly and logs a warning for any domain with null domainName
8. **Branch**: all changes go to `claude/synergy-api-compliance-plan-sYaYJ`
