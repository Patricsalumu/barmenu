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
        $company = Company::with([
            'categories.children',
            'categories.products' => fn ($query) => $query->where('is_available', true)->orderBy('name'),
        ])->firstOrFail();

        $categories = $company->categories->whereNull('parent_id')->values();
        $selectedCategory = $request->integer('category');
        $search = trim((string) $request->query('q', ''));

        if ($selectedCategory && ! Category::where('company_id', $company->id)->whereKey($selectedCategory)->exists()) {
            abort(404);
        }

        $products = $company->products()
            ->with('category.parent')
            ->where('is_available', true)
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $category = Category::findOrFail($selectedCategory);
                $ids = $category->parent_id
                    ? [$category->id]
                    : $category->children()->pluck('id')->prepend($category->id)->all();

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

        return view('menu.index', compact('company', 'categories', 'products', 'selectedCategory', 'search'));
    }
}
