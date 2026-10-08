<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ResolveCompany;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View|Response
    {
        $company = $request->attributes->get('company') ?? ResolveCompany::currentCompany($request);

        if (! $company) {
            if (ResolveCompany::isTenantRequest($request)) {
                abort(404, 'Entreprise introuvable.');
            }

            return response()->view('menu.landing');
        }

        $selectedCategory = $request->integer('category');
        $search = trim((string) $request->query('q', ''));
        $filterCategory = null;

        if ($selectedCategory) {
            $filterCategory = $company->categories()->findOrFail($selectedCategory);
        }
        $expandedCategoryId = $filterCategory
            ? ($filterCategory->parent_id ?? $filterCategory->id)
            : null;

        $products = $company->products()
            ->with('category.parent')
            ->where('is_available', true)
            ->when($filterCategory, function ($query) use ($filterCategory) {
                $ids = $filterCategory->parent_id
                    ? [$filterCategory->id]
                    : $filterCategory->children()->pluck('id')->prepend($filterCategory->id)->all();

                $query->whereIn('category_id', $ids);
            })
            ->when($search !== '', fn ($query) => $query->where(function ($products) use ($search) {
                $products->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            return response()->view('menu.results', compact('company', 'products'));
        }

        $categories = $company->categories()->with('children')->get()->whereNull('parent_id')->values();

        return view('menu.index', compact('company', 'categories', 'products', 'selectedCategory', 'expandedCategoryId', 'search'));
    }
}
