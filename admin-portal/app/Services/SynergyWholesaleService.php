<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use SynergyWholesale\SynergyWholesale;
use SynergyWholesale\Commands\BalanceQueryCommand;
use SynergyWholesale\Commands\CheckDomainCommand;
use SynergyWholesale\Commands\DomainInfoCommand;
use SynergyWholesale\Commands\ListDomainsCommand;
use SynergyWholesale\Commands\RenewDomainCommand;
use SynergyWholesale\Commands\UpdateNameServersCommand;
use SynergyWholesale\Commands\EnableAutoRenewalCommand;
use SynergyWholesale\Commands\DisableAutoRenewalCommand;
use SynergyWholesale\Commands\LockDomainCommand;
use SynergyWholesale\Commands\UnlockDomainCommand;
use SynergyWholesale\Commands\GetDomainPricingCommand;
use SynergyWholesale\Commands\DomainRegisterCommand;
use SynergyWholesale\Commands\DomainRegisterAUCommand;
use SynergyWholesale\Types\Domain;
use SynergyWholesale\Types\DomainList;
use SynergyWholesale\Types\RegistrationYears;
use SynergyWholesale\Types\Contact;
use SynergyWholesale\Types\AuContact;
use SynergyWholesale\Types\AuRegistrant;

class SynergyWholesaleService
{
    public function __construct(private SynergyWholesale $api) {}

    // -------------------------------------------------------------------------
    // Account
    // -------------------------------------------------------------------------

    public function getBalance(): ?float
    {
        try {
            $response = $this->api->execute(new BalanceQueryCommand());
            return (float) $response->getBalance();
        } catch (Exception $e) {
            Log::error('Synergy::getBalance failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function testConnection(): bool
    {
        return $this->getBalance() !== null;
    }

    // -------------------------------------------------------------------------
    // Domains
    // -------------------------------------------------------------------------

    public function checkDomainAvailability(string $domainName): ?bool
    {
        try {
            $response = $this->api->execute(new CheckDomainCommand(new Domain($domainName)));
            return $response->isAvailable();
        } catch (Exception $e) {
            Log::error('Synergy::checkDomainAvailability failed', [
                'domain' => $domainName,
                'error'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function getDomainInfo(string $domainName): mixed
    {
        try {
            return $this->api->execute(new DomainInfoCommand(new Domain($domainName)));
        } catch (Exception $e) {
            Log::error('Synergy::getDomainInfo failed', [
                'domain' => $domainName,
                'error'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function listDomains(int $page = 1, int $limit = 100, ?string $status = null): ?array
    {
        try {
            $response = $this->api->execute(new ListDomainsCommand($page, $limit, $status));
            return [
                'domains' => $response->getDomainList() ?? [],
                'page'    => $page,
                'limit'   => $limit,
            ];
        } catch (Exception $e) {
            Log::error('Synergy::listDomains failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function registerDomain(
        string $domainName,
        int $years,
        array $nameservers,
        bool $idProtect,
        array $contact
    ): mixed {
        try {
            $c = new Contact(
                $contact['firstName'],
                $contact['lastName'],
                new \SynergyWholesale\Types\Email($contact['email']),
                new \SynergyWholesale\Types\Phone($contact['phone']),
                $contact['address1'],
                $contact['city'],
                $contact['state'],
                $contact['postcode'],
                new \SynergyWholesale\Types\Country($contact['country'])
            );

            return $this->api->execute(new DomainRegisterCommand(
                new Domain($domainName),
                new RegistrationYears($years),
                new DomainList($nameservers),
                $idProtect ? 'Y' : 'N',
                $c, $c, $c, $c
            ));
        } catch (Exception $e) {
            Log::error('Synergy::registerDomain failed', [
                'domain' => $domainName,
                'error'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function renewDomain(string $domainName, int $years = 1): mixed
    {
        try {
            return $this->api->execute(new RenewDomainCommand(
                new Domain($domainName),
                new RegistrationYears($years)
            ));
        } catch (Exception $e) {
            Log::error('Synergy::renewDomain failed', [
                'domain' => $domainName,
                'error'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function updateNameservers(string $domainName, array $nameservers): bool
    {
        try {
            $this->api->execute(new UpdateNameServersCommand(
                new Domain($domainName),
                new DomainList($nameservers)
            ));
            return true;
        } catch (Exception $e) {
            Log::error('Synergy::updateNameservers failed', [
                'domain' => $domainName,
                'error'  => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function enableAutoRenewal(string $domainName): bool
    {
        try {
            $this->api->execute(new EnableAutoRenewalCommand(new Domain($domainName)));
            return true;
        } catch (Exception $e) {
            Log::error('Synergy::enableAutoRenewal failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function disableAutoRenewal(string $domainName): bool
    {
        try {
            $this->api->execute(new DisableAutoRenewalCommand(new Domain($domainName)));
            return true;
        } catch (Exception $e) {
            Log::error('Synergy::disableAutoRenewal failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function lockDomain(string $domainName): bool
    {
        try {
            $this->api->execute(new LockDomainCommand(new Domain($domainName)));
            return true;
        } catch (Exception $e) {
            Log::error('Synergy::lockDomain failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function unlockDomain(string $domainName): bool
    {
        try {
            $this->api->execute(new UnlockDomainCommand(new Domain($domainName)));
            return true;
        } catch (Exception $e) {
            Log::error('Synergy::unlockDomain failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getDomainPricing(): mixed
    {
        try {
            return $this->api->execute(new GetDomainPricingCommand());
        } catch (Exception $e) {
            Log::error('Synergy::getDomainPricing failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
