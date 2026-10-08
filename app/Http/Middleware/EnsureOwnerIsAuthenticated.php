<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('owner_authenticated', false)) {
            return redirect()->route('owner.login');
        }

        $company = $request->attributes->get('company');
        $ownerCompanyId = $request->session()->get('owner_company_id');

        if ($company && $ownerCompanyId !== null && (int) $ownerCompanyId !== (int) $company->id) {
            $request->session()->forget(['owner_authenticated', 'owner_company_id']);

            return redirect()->route('owner.login')->withErrors([
                'code' => 'Session invalide pour cette entreprise.',
            ]);
        }

        return $next($request);
    }
}
