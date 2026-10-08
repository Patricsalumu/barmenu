<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('super_admin_authenticated', false)) {
            return redirect()->route('super-admin.login');
        }

        return $next($request);
    }
}
