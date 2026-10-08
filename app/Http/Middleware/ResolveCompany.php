<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCompany
{
    public static function currentCompany(Request $request): ?Company
    {
        $company = $request->attributes->get('company');

        if ($company instanceof Company) {
            return $company;
        }

        $host = strtolower((string) $request->getHost());
        $host = preg_replace('/:\d+$/', '', $host) ?: $host;

        if ($host === '' || $host === 'localhost' || $host === '127.0.0.1' || $host === '[::1]') {
            return Company::query()
                ->where('subdomain', config('app.default_tenant_subdomain', 'lecomptoir'))
                ->first();
        }

        foreach (static::tenantBaseDomains() as $baseDomain) {
            $domain = strtolower((string) $baseDomain);

            if ($host === $domain || $host === 'www.' . $domain) {
                return null;
            }

            if (str_ends_with($host, '.' . $domain)) {
                $subdomain = substr($host, 0, -strlen('.' . $domain));

                if ($subdomain !== '' && $subdomain !== 'www') {
                    return Company::query()->where('subdomain', $subdomain)->first();
                }

                return null;
            }
        }

        return null;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $company = static::currentCompany($request);

        if (! $company && static::isTenantRequest($request)) {
            abort(404, 'Entreprise introuvable.');
        }

        if ($company) {
            $request->attributes->set('company', $company);
        }

        return $next($request);
    }

    public static function isTenantRequest(Request $request): bool
    {
        $host = strtolower((string) $request->getHost());
        $host = preg_replace('/:\d+$/', '', $host) ?: $host;

        foreach (static::tenantBaseDomains() as $baseDomain) {
            $domain = strtolower((string) $baseDomain);

            if ($host === $domain || $host === 'www.' . $domain) {
                return false;
            }

            if (str_ends_with($host, '.' . $domain)) {
                $subdomain = substr($host, 0, -strlen('.' . $domain));

                return $subdomain !== '' && $subdomain !== 'www';
            }
        }

        return false;
    }

    protected static function tenantBaseDomains(): array
    {
        $domains = config('app.tenant_domains', 'barmenu.site,barmenu.test');

        return array_values(array_filter(array_map(static fn (string $value): string => trim(strtolower($value)), preg_split('/\s*,\s*/', $domains) ?: [])));
    }
}