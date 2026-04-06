# Synergy Wholesale API Compliance Report

**Project:** FCPR_SynergyPortal  
**API Version Audited:** Synergy Wholesale API v3.15  
**Laravel Version:** 13 (PHP 8.3)  
**Package:** `hampel/synergy-wholesale ^1.5`  
**Audit Date:** 2026-04-07  

---

## Executive Summary

The project correctly uses the `hampel/synergy-wholesale` SOAP package as its API transport layer — the fundamental architecture is compliant. The SOAP wrapper handles authentication, request serialisation, and error responses correctly via `SynergyServiceProvider`.

However, three major feature areas (DNS, hosting, and SSL) are **not connected to the Synergy API at all** — they write exclusively to a local database. Several secondary issues exist around parameter correctness, deprecated imports, and a pagination bug. These are all fixable with targeted refactoring in Claude Code.

---

## Issue Index

| # | Severity | Area | Issue |
|---|---|---|---|
| 1 | 🔴 Critical | DNS | DNS operations not API-backed |
| 2 | 🔴 Critical | Hosting | Hosting operations not API-backed |
| 3 | 🔴 Critical | SSL | SSL operations not API-backed |
| 4 | 🔴 Critical | Domains | `listDomains` limit exceeds API maximum |
| 5 | 🟠 Moderate | Domains | `.au` domain registration missing eligibility handling |
| 6 | 🟠 Moderate | Domains | Domain registration passes single Contact for all four roles |
| 7 | 🟠 Moderate | Hosting | `cPanelLogin` uses static URL instead of `hostingGetLogin` API |
| 8 | 🟠 Moderate | DNS | NS and SRV record types unsupported by API |
| 9 | 🟠 Moderate | DNS | Local field names don't match API field names |
| 10 | 🟡 Low | Codebase | Dead deprecated imports in both `SynergyWholesaleService` files |
| 11 | 🟡 Low | Infrastructure | Admin portal missing domain-related migrations |
| 12 | 🟡 Low | Codebase | `SynergyWholesaleService` duplicated verbatim in both portals |
| 13 | 🟡 Low | Domains | `syncDomains` field mapping relies on fragile property guessing |

---

## Detailed Findings

---

### 🔴 Issue 1 — DNS Operations Not API-Backed

**Files:** `admin-portal/app/Http/Controllers/Admin/DnsController.php`  
`customer-portal/app/Http/Controllers/Customer/DnsController.php`

**Description:**  
Both `DnsController` implementations perform all create/update/delete operations exclusively against the local `dns_records` table. No Synergy API commands are called. DNS records added through the portal **will not appear in Synergy's DNS cluster** and will have no effect on live DNS resolution.

The API provides the following commands for DNS zone and record management:

| API Command | Purpose |
|---|---|
| `addDNSZone` | Create a DNS zone for a domain (prerequisite before adding records) |
| `deleteDNSZone` | Delete an entire DNS zone |
| `addDNSRecord` | Add a record to an existing zone |
| `updateDNSRecord` | Update an existing record by ID |
| `deleteDNSRecord` | Delete a record by ID |
| `getDNSRecord` | Retrieve a record by ID |
| `listDNSZone` | List all records in a zone |

**Required refactoring:**

1. Add the following methods to `SynergyWholesaleService`:
   - `addDnsZone(string $domain): bool`
   - `addDnsRecord(string $domain, string $recordName, string $recordType, string $recordContent, int $recordTTL, ?int $recordPrio): mixed`
   - `updateDnsRecord(string $domain, int $recordId, ...): bool`
   - `deleteDnsRecord(string $domain, int $recordId): bool`
   - `listDnsZone(string $domain): mixed`

2. In `DnsController::store()`: call `$this->synergy->addDnsRecord(...)` before or instead of writing to local DB. If the zone doesn't exist, call `addDnsZone` first.

3. In `DnsController::destroy()`: call `$this->synergy->deleteDnsRecord(...)` before deleting locally.

4. Store the API-returned record ID in the local `dns_records` table so that update/delete calls can reference it.

5. On the `DomainController::show()` page, consider pulling live DNS records via `listDNSZone` rather than from local DB.

---

### 🔴 Issue 2 — Hosting Operations Not API-Backed

**Files:** `admin-portal/app/Http/Controllers/Admin/HostingController.php`  
`customer-portal/app/Http/Controllers/Customer/HostingController.php`

**Description:**  
Hosting accounts are created, edited, and status-changed through local DB operations only. The `HostingController::store()` method creates a `HostingAccount` record with manually entered data (plan, username, URL). No Synergy API calls are made to actually provision the service.

The API provides:

| API Command | Purpose |
|---|---|
| `hostingPurchaseService` | Provision a new cPanel or email hosting account |
| `hostingSuspendService` | Suspend an account |
| `hostingUnsuspendService` | Unsuspend an account |
| `hostingChangePackage` | Change the hosting plan |
| `hostingChangePassword` | Change cPanel password |
| `hostingGetLogin` | Generate authenticated SSO login URL |
| `hostingListPackages` | Retrieve list of configured hosting plans |
| `hostingGetService` | Query an existing service |
| `listHosting` | List all hosting accounts |
| `hostingTerminateService` | Terminate a service |

**Required refactoring:**

1. Add the following methods to `SynergyWholesaleService`:
   - `purchaseHosting(string $planName, string $domain, string $email, ?string $username, ?string $password): mixed`
   - `suspendHosting(string $identifier): bool`
   - `unsuspendHosting(string $identifier): bool`
   - `getHostingLoginUrl(string $identifier): ?string`
   - `listHostingPackages(): mixed`

2. In `HostingController::store()`: Call `purchaseHosting()` and populate `cpanel_username`, `synergy_ref` (hoid), and `cpanel_url` from the API response rather than manual entry.

3. In `HostingController::updateStatus()`: When status changes to `suspended` or `active`, call `suspendHosting()` / `unsuspendHosting()` on the API.

4. Replace the plan name text input with a dropdown populated from `hostingListPackages` so plan names exactly match what Synergy has configured.

---

### 🔴 Issue 3 — SSL Operations Not API-Backed

**Files:** `admin-portal/app/Http/Controllers/Admin/SslController.php`  
`customer-portal/app/Http/Controllers/Customer/SslController.php`

**Description:**  
SSL certificates are tracked as local records only. `SslController::store()` creates a local `SslCertificate` row with a manually entered type and optional reference. No API commands are called for CSR generation, certificate purchase, status checking, or renewal.

The API provides:

| API Command | Purpose |
|---|---|
| `SSL_generateCSR` | Generate a Certificate Signing Request |
| `SSL_decodeCSR` | Decode/validate an existing CSR |
| `SSL_purchaseSSLCertificate` | Purchase and initiate issuance |
| `SSL_getSSLCertificate` | Query certificate status and details |
| `SSL_getCertSimpleStatus` | Lightweight status check |
| `SSL_listAllCerts` | List all certificates under the account |
| `SSL_cancelSSLCertificate` | Cancel a certificate |
| `SSL_renewSSLCertificate` | Renew an existing certificate |
| `SSL_reissueCertificate` | Reissue a certificate |
| `SSL_resendDVEmail` | Resend domain validation email |
| `getSSLPricing` | Get product pricing |

**Required refactoring:**

1. Add to `SynergyWholesaleService`:
   - `generateCsr(string $domain, string $org, string $city, string $state, string $country, string $email, int $years): mixed`
   - `purchaseSslCertificate(string $productId, string $csr, ...): mixed`
   - `getSslCertificate(int $sslId): mixed`
   - `listSslCertificates(): mixed`

2. Refactor `SslController::store()` to call `generateCsr()` then `purchaseSslCertificate()` and store the returned SSL ID as `synergy_ref`.

3. Add a route to query live status via `SSL_getSSLCertificate` on the show page.

4. Populate SSL product types from `getSSLPricing` rather than a hardcoded `in:DV,OV,EV,Wildcard` enum.

---

### 🔴 Issue 4 — `listDomains` Limit Exceeds API Maximum

**File:** `admin-portal/app/Http/Controllers/Admin/SynergyController.php` (line ~20)  
`admin-portal/app/Services/SynergyWholesaleService.php`

**Description:**  
`syncDomains()` calls `$this->synergy->listDomains(1, 1000)`. The API documentation explicitly states the maximum per-page limit is **500**. Requesting 1000 will either be capped at 500 (silently returning an incomplete result set) or cause an API error, leaving a reseller with more than 500 domains unable to fully sync.

**Required refactoring:**

```php
// SynergyController::syncDomains() — replace single call with pagination loop
$page   = 1;
$limit  = 500; // API maximum
$all    = [];

do {
    $result = $this->synergy->listDomains($page, $limit);
    if (!$result) break;
    $batch = (array) $result['domains'];
    $all   = array_merge($all, $batch);
    $page++;
} while (count($batch) === $limit); // stop when a partial page is returned
```

---

### 🟠 Issue 5 — `.au` Domain Registration Missing Eligibility Handling

**File:** `admin-portal/app/Services/SynergyWholesaleService.php`  
`admin-portal/app/Http/Controllers/Admin/DomainController.php`

**Description:**  
The `registerDomain()` method calls `DomainRegisterCommand` for all TLDs without checking whether the domain is a `.au` extension. `.au` domains (`.com.au`, `.net.au`, `.org.au`, `.asn.au`, etc.) require registrant eligibility information: ABN/ACN, registrant type, and registrant name. Without this, `.au` registrations will fail at the API level.

Both `SynergyWholesaleService` files also import `DomainRegisterAUCommand`, `AuContact`, and `AuRegistrant` but never use them. `DomainRegisterAUCommand` is deprecated since API v3.4; the current approach is to pass eligibility parameters through the generic `domainRegister` command.

**Required refactoring:**

1. Remove the dead imports: `DomainRegisterAUCommand`, `AuContact`, `AuRegistrant`.

2. In `DomainController` (admin), detect `.au` TLD:
   ```php
   $isAu = preg_match('/\.(com|net|org|asn|id)\.au$/', $validated['domain_name']);
   ```

3. Add eligibility fields to the registration form when `.au` is detected:
   - `registrant_type` (ABN, ACN, RBN, TM, etc.)
   - `registrant_id` (the ABN/ACN/RBN number)
   - `registrant_name` (the eligible entity name)
   - `eligibility_type` (commercial, non-commercial, etc.)

4. Pass these as the `eligibility` array to `DomainRegisterCommand`. Refer to the API's `.AU Domain Registration Information` section for full field requirements.

5. Consider using `businessCheckRegistration` (`Command: businessCheckRegistration`) to validate an ABN/ACN before attempting registration.

---

### 🟠 Issue 6 — Domain Registration Passes Same Contact for All Four Roles

**File:** `admin-portal/app/Services/SynergyWholesaleService.php` (`registerDomain()`)

**Description:**  
The `registerDomain()` method creates one `Contact` object `$c` and passes it to `DomainRegisterCommand` four times:

```php
return $this->api->execute(new DomainRegisterCommand(
    ...,
    $c, $c, $c, $c  // registrant, admin, tech, billing — all identical
));
```

The `domainRegister` command accepts separate registrant, admin, technical, and billing contacts. Passing the same object for all roles is not an API error but it means the registered admin and technical contacts always mirror the registrant — which may not be correct, particularly for managed clients where the reseller is the admin/tech contact.

**Required refactoring:**

Allow the caller to pass separate contacts, or at minimum allow an override for admin/technical contact so the reseller's own details can be used for those roles while the customer is the registrant. Update `DomainController::storeDomain()` to collect or inject the separate contacts as appropriate.

---

### 🟠 Issue 7 — `cPanelLogin` Uses Static URL Instead of `hostingGetLogin`

**File:** `customer-portal/app/Http/Controllers/Customer/HostingController.php`

**Description:**  
The `cPanelLogin()` method redirects the customer to a stored static `cpanel_url` string:

```php
if ($account->cpanel_url) {
    return redirect()->away($account->cpanel_url);
}
```

This URL will never contain a valid session token and will direct the user to a cPanel login screen, not an authenticated session. The API provides `hostingGetLogin` (`Command: hostingGetLogin`) which generates a fresh, time-limited, authenticated SSO URL for direct cPanel access. This is the correct implementation.

**Required refactoring:**

```php
public function cPanelLogin(int $id): RedirectResponse
{
    $account = auth()->user()->hostingAccounts()->findOrFail($id);
    $loginUrl = $this->synergy->getHostingLoginUrl($account->synergy_ref ?? $account->domain);

    if ($loginUrl) {
        return redirect()->away($loginUrl);
    }

    return back()->with('error', 'Could not generate login link. Please try again.');
}
```

`SynergyWholesaleService::getHostingLoginUrl(string $identifier)` should call `hostingGetLogin` with the account's `hoid` or email identifier and return the `url` field from the response.

---

### 🟠 Issue 8 — NS and SRV Record Types Not Supported by API

**Files:** `admin-portal/app/Http/Controllers/Admin/DnsController.php`  
`customer-portal/app/Http/Controllers/Customer/DnsController.php`  
`admin-portal/resources/views/dns/show.blade.php`  
`customer-portal/resources/views/dns/create.blade.php`

**Description:**  
The UI and validation rules allow `NS` and `SRV` record types:

```php
'type' => 'required|in:A,AAAA,CNAME,MX,TXT,NS,SRV',
```

The Synergy API `addDNSRecord` and `updateDNSRecord` commands only accept: **A, AAAA, CNAME, MX, TXT**. Submitting NS or SRV records will fail at the API level once DNS is wired up.

**Required refactoring:**

Remove NS and SRV from the validation rule and the Blade select dropdown:

```php
'type' => 'required|in:A,AAAA,CNAME,MX,TXT',
```

---

### 🟠 Issue 9 — Local DNS Field Names Don't Match API Field Names

**Files:** `customer-portal/database/migrations/2024_01_01_000005_create_dns_records_table.php`  
`admin-portal/app/Http/Controllers/Admin/DnsController.php` (store method)

**Description:**  
The `dns_records` table uses column names that do not match the Synergy API's parameter names for `addDNSRecord` / `updateDNSRecord`:

| Local column | API parameter | Notes |
|---|---|---|
| `host` | `recordName` | Hostname / subdomain |
| `type` | `recordType` | Record type |
| `content` | `recordContent` | Record value |
| `ttl` | `recordTTL` | Time to live |
| `priority` | `recordPrio` | MX priority (MX only) |

This mismatch means any future code that passes local model fields directly to the API will have incorrect parameter keys and fail silently or with `ERR_VAR_EMPTY`.

**Required refactoring:**

When calling `addDnsRecord()` or `updateDnsRecord()` in `SynergyWholesaleService`, explicitly map local field names to API parameter names:

```php
public function addDnsRecord(DnsRecord $record): mixed
{
    return $this->api->execute(new AddDNSRecordCommand([
        'domainName'    => $record->domain_name,
        'recordName'    => $record->host,       // ← map here
        'recordType'    => $record->type,
        'recordContent' => $record->content,
        'recordTTL'     => $record->ttl,
        'recordPrio'    => $record->priority,   // only for MX
    ]));
}
```

Alternatively, rename the database columns to match the API parameters (migration + model + view updates required).

---

### 🟡 Issue 10 — Dead Deprecated Imports in `SynergyWholesaleService`

**Files:** `admin-portal/app/Services/SynergyWholesaleService.php`  
`customer-portal/app/Services/SynergyWholesaleService.php`

**Description:**  
Both files import three symbols that are never used:

```php
use SynergyWholesale\Commands\DomainRegisterAUCommand;  // deprecated since API v3.4
use SynergyWholesale\Types\AuContact;
use SynergyWholesale\Types\AuRegistrant;
```

`DomainRegisterAUCommand` is deprecated and replaced by the generic `domainRegister` with eligibility parameters. Its presence suggests incomplete .au implementation work.

**Required refactoring:**  
Remove these three imports from both files. Implement proper .au eligibility handling as described in Issue 5.

---

### 🟡 Issue 11 — Admin Portal Missing All Domain-Related Migrations

**Directory:** `admin-portal/database/migrations/`

**Description:**  
The admin portal only contains the three base Laravel migrations (users, cache, jobs). All application-level tables — `domains`, `hosting_accounts`, `ssl_certificates`, `dns_records`, `invoices`, `invoice_items`, `activity_log` — are defined exclusively in the customer portal's migration set.

This creates a deployment dependency: the admin portal **cannot be set up independently**. If someone clones only the admin portal and runs `php artisan migrate`, none of the application tables will exist. The admin portal's `Domain`, `HostingAccount`, `SslCertificate`, `DnsRecord`, `Invoice` models will throw errors.

**Required refactoring (choose one approach):**

**Option A (Recommended) — Shared migrations directory:**  
Move all shared migrations to a common location outside both apps (e.g. `../database/migrations/`) and configure both `config/database.php` files to point to it via `migration_paths`.

**Option B — Duplicate migrations in admin portal:**  
Copy the customer portal's application migrations into the admin portal with the same filenames. They are idempotent (wrapped in `Schema::create` with no-op on existing tables) — but this creates a maintenance burden keeping them in sync.

**Option C — Document the dependency clearly:**  
If the shared-DB architecture is intentional and the portals will always be deployed together, at minimum add a `README` note and a check in the admin portal's `AppServiceProvider` that validates the expected tables exist.

---

### 🟡 Issue 12 — `SynergyWholesaleService` Duplicated in Both Portals

**Files:** `admin-portal/app/Services/SynergyWholesaleService.php`  
`customer-portal/app/Services/SynergyWholesaleService.php`

**Description:**  
The two files are **byte-for-byte identical**. Any change to one must be manually replicated to the other. This is a maintenance risk — divergence will eventually occur.

**Required refactoring (choose one):**

**Option A — Shared Composer package:**  
Extract `SynergyWholesaleService` and `SynergyServiceProvider` into a local Composer path package (e.g. `../packages/synergy-service/`) and require it in both portals' `composer.json` via a path repository.

**Option B — Symlink:**  
If the portals are always deployed together on the same filesystem, symlink the service file from one portal into the other. Less clean but zero overhead.

**Option C — Accept duplication with a comment:**  
Add a comment block to both files noting they must be kept in sync, and add a CI check (e.g. `diff` assertion) to catch divergence.

---

### 🟡 Issue 13 — `syncDomains` Field Mapping Fragility

**File:** `admin-portal/app/Http/Controllers/Admin/SynergyController.php`

**Description:**  
The `syncDomains()` method accesses domain data from the API response object using:

```php
$domainName = $item->domainName ?? $item->domain ?? null;
```

The API documentation specifies `domainName` (camelCase) as the returned field for `listDomains`. The fallback `$item->domain` is not a documented field name and may silently fail to retrieve domain names on some SDK versions. Similarly, `$item->domain_expiry` (snake_case) is used for the expiry field, but the API docs specify `domain_expiry` — check the actual SOAP response object property names against the `hampel/synergy-wholesale` package's response models.

**Required refactoring:**

Inspect the `ListDomainsResponse` object returned by the package to confirm the exact PHP property names, then use those exclusively without fragile fallbacks. Add logging when `$domainName` is null to catch mapping failures rather than silently skipping domains.

---

## Refactoring Priority Order

For Claude Code sessions, address in this order:

1. **Issue 4** — Fix `listDomains` pagination (15 min, isolated change)
2. **Issue 10** — Remove dead imports (5 min, cosmetic)
3. **Issue 13** — Fix `syncDomains` field mapping (20 min, isolated)
4. **Issue 8** — Remove NS/SRV from DNS form validation (10 min)
5. **Issue 7** — Implement `hostingGetLogin` for cPanel SSO (30 min)
6. **Issue 6** — Allow separate contacts in domain registration (45 min)
7. **Issue 2** — Wire hosting to API (`hostingPurchaseService` etc.) (2–4 hrs)
8. **Issue 3** — Wire SSL to API (2–4 hrs)
9. **Issue 1** — Wire DNS to API (3–5 hrs, requires zone management logic)
10. **Issue 5** — Add .au eligibility to domain registration (2–3 hrs)
11. **Issue 9** — Align DNS field names with API params (1 hr, migration required)
12. **Issue 11** — Resolve admin portal migration dependency (1 hr)
13. **Issue 12** — Extract shared service (1–2 hrs)