<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ResolveCompany;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OwnerController extends Controller
{
    public function login(Request $request): View
    {
        $company = ResolveCompany::currentCompany($request);

        abort_unless($company, 404, 'Entreprise introuvable.');

        return view('owner.login', compact('company'));
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $company = ResolveCompany::currentCompany($request);

        abort_unless($company, 404, 'Entreprise introuvable.');

        $credentials = $request->validate(['code' => ['required', 'string', 'max:30']]);

        if (! Hash::check($credentials['code'], $company->owner_code)) {
            return back()->withErrors(['code' => 'Code incorrect. Veuillez réessayer.']);
        }

        $request->session()->regenerate();
        $request->session()->put('owner_authenticated', true);
        $request->session()->put('owner_company_id', $company->id);

        return redirect()->route('owner.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('menu');
    }

    public function dashboard(Request $request): View
    {
        $company = $this->currentCompany($request);
        $company->load(['categories.children', 'categories.products']);
        $categories = $company->categories;

        return view('owner.dashboard', compact('company', 'categories'));
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        $company = $this->currentCompany($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'activity_type' => ['required', 'string', 'max:100'],
            'slogan' => ['nullable', 'string', 'max:180'],
            'menu_tagline' => ['nullable', 'string', 'max:180'],
            'address' => ['nullable', 'string', 'max:180'],
            'whatsapp_number' => ['nullable', 'string', 'max:16', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'theme_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'currency' => ['required', Rule::in(['$', 'F'])],
            'exchange_rate' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'owner_code' => ['nullable', 'string', 'min:4', 'max:30'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }
        unset($data['logo']);

        if (! empty($data['whatsapp_number'])) {
            $data['whatsapp_number'] = ltrim($data['whatsapp_number'], '+');
        }

        if (empty($data['owner_code'])) {
            unset($data['owner_code']);
        }

        $company->update($data);

        return back()->with('success', 'Les informations de l’établissement ont été mises à jour.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $company = $this->currentCompany($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('company_id', $company->id)->whereNull('parent_id')),
            ],
        ]);
        $company->categories()->create($data);

        return back()->with('success', 'La catégorie a été ajoutée.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $company = $this->currentCompany($request);
        $this->ensureCompanyCategory($company, $category);
        $data = $request->validate(['name' => ['required', 'string', 'max:80']]);
        $category->update($data);

        return back()->with('success', 'La catégorie a été modifiée.');
    }

    public function deleteCategory(Request $request, Category $category): RedirectResponse
    {
        $company = $this->currentCompany($request);
        $this->ensureCompanyCategory($company, $category);
        $category->children()->delete();
        $category->delete();

        return back()->with('success', 'La catégorie et ses produits ont été supprimés.');
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $company = $this->currentCompany($request);
        $data = $this->validatedProduct($request, $company);
        $company->products()->create($data);

        return back()->with('success', 'Le produit a été ajouté.');
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $company = $this->currentCompany($request);
        $product = $company->products()->findOrFail($product->id);
        $data = $this->validatedProduct($request, $company, false);
        $product->update($data);

        return back()->with('success', 'Le produit a été modifié.');
    }

    public function deleteProduct(Request $request, Product $product): RedirectResponse
    {
        $company = $this->currentCompany($request);
        $product = $company->products()->findOrFail($product->id);
        $product->delete();

        return back()->with('success', 'Le produit a été supprimé.');
    }

    private function validatedProduct(Request $request, Company $company, bool $defaultAvailable = true): array
    {
        $data = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('company_id', $company->id)),
            ],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'is_available' => ['nullable', 'boolean'],
        ]);
        $data['is_available'] = $request->has('is_available')
            ? $request->boolean('is_available')
            : $defaultAvailable;

        return $data;
    }

    private function ensureCompanyCategory(Company $company, Category $category): void
    {
        abort_unless($category->company_id === $company->id, 404);
    }

    private function currentCompany(Request $request): Company
    {
        $company = $request->attributes->get('company') ?? ResolveCompany::currentCompany($request);

        abort_unless($company instanceof Company, 404, 'Entreprise introuvable.');

        return $company;
    }
}
