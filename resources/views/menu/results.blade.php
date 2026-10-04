<div id="menu-results-content" data-count-label="{{ $products->count() }} produit{{ $products->count() > 1 ? 's' : '' }} disponible{{ $products->count() > 1 ? 's' : '' }}">
    <div class="product-grid">
        @forelse($products as $product)
            <article class="product-card">
                <div class="product-top">
                    <div><div class="category-label">{{ $product->category->parent?->name ?? $product->category->name }}{{ $product->category->parent ? ' · '.$product->category->name : '' }}</div><h3>{{ $product->name }}</h3></div>
                    <span class="price">{{ number_format((float) $product->price, 0, ',', ' ') }} {{ $company->currency }}</span>
                </div>
                @if($product->description)<p>{{ $product->description }}</p>@endif
            </article>
        @empty
            <div class="empty"><div class="eyebrow">Oups…</div><h3>Aucun produit trouvé</h3><p>Essayez une autre catégorie ou une autre recherche.</p></div>
        @endforelse
    </div>
</div>
