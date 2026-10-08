<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function login(): View
    {
        return view('super-admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $expectedPassword = env('SUPER_ADMIN_PASSWORD', '123456');

        if (($credentials['password'] ?? '') !== (string) $expectedPassword) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        $request->session()->regenerate();
        $request->session()->put('super_admin_authenticated', true);

        return redirect()->route('super-admin.dashboard');
    }

    public function dashboard(): View
    {
        $companies = Company::orderBy('name')->get();

        return view('super-admin.dashboard', compact('companies'));
    }

    public function storeCompany(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('companies', 'subdomain'),
                'not_in:www',
            ],
            'activity_type' => ['required', 'string', 'max:100'],
            'slogan' => ['nullable', 'string', 'max:180'],
            'menu_tagline' => ['nullable', 'string', 'max:180'],
            'address' => ['nullable', 'string', 'max:180'],
            'theme_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'currency' => ['required', 'string', 'max:3'],
            'exchange_rate' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'owner_code' => ['required', 'string', 'min:4', 'max:30'],
        ]);

        $data['subdomain'] = strtolower($data['subdomain']);

        Company::create($data);

        return redirect()->route('super-admin.dashboard')->with('success', 'L’entreprise a été créée avec succès.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('super_admin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login');
    }
}
