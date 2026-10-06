<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View|Response
    {
        $company = Company::firstOrFail();

        $selectedCategory = $request->integer('category');
        $search = trim((string) $request->query('q', ''));
        $filterCategory = null;

        if ($selectedCategory) {
            $filterCategory = Category::where('company_id', $company->id)->findOrFail($selectedCategory);
        }

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

        return view('menu.index', compact('company', 'categories', 'products', 'selectedCategory', 'search'));
    }
}
