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

        return $next($request);
    }
}
