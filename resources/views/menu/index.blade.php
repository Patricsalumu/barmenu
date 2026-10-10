@extends('layouts.app')

@section('title', $company->name)

@section('content')
    <main>
        @if($company->menu_layout === 'category_grid')
            @php
                $selectedParentCategory = $categories->firstWhere('id', $expandedCategoryId);
                $showGridHome = ! $selectedCategory && $search === '';
                $showGridSubcategories = $selectedCategory
                    && $selectedCategory === $expandedCategoryId
                    && $selectedParentCategory?->children->isNotEmpty()
                    && $search === '';
            @endphp
            <section class="grid-menu-intro" aria-label="Présentation de {{ $company->name }}">
                @if($company->logo_path)
                    <img class="grid-menu-logo" src="{{ asset('storage/'.$company->logo_path) }}" alt="Logo {{ $company->name }}">
                @else
                    <div class="grid-menu-logo-placeholder" aria-hidden="true">{{ mb_substr($company->name, 0, 1) }}</div>
                @endif
                <div>
                    <div class="eyebrow">{{ $company->activity_type }}</div>
                    <h1>{{ $company->name }}</h1>
                    @if($company->slogan)<p>{{ $company->slogan }}</p>@endif
                    <div class="grid-menu-meta">
                        @if($company->address)<span>⌖ {{ $company->address }}</span>@endif
                        @if($company->menu_tagline)<span>{{ $company->menu_tagline }}</span>@endif
                    </div>
                    @if($company->whatsapp_number)
                        <div class="grid-whatsapp-cta">
                            <p>Une question, une réclamation ou une réservation ?</p>
                            <a class="button" href="https://wa.me/{{ $company->whatsapp_number }}?text={{ rawurlencode('Salut '.$company->name.', je vous contacte au sujet d’une question, d’une réclamation ou d’une réservation.') }}" target="_blank" rel="noopener noreferrer" aria-label="Contacter {{ $company->name }} par WhatsApp pour une question, une réclamation ou une réservation">
                                Contacter sur WhatsApp ↗
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            <form class="search-form grid-search" method="GET" action="{{ route('menu') }}" role="search">
                @if($selectedCategory)<input type="hidden" name="category" value="{{ $selectedCategory }}">@endif
                <input class="field" type="search" name="q" value="{{ $search }}" placeholder="Rechercher un produit…" aria-label="Rechercher un produit">
            </form>

            @if($showGridHome)
                <section class="grid-menu-section" aria-labelledby="category-heading">
                    <div class="grid-menu-heading"><div><div class="eyebrow">À découvrir</div><h2 id="category-heading">Nos catégories</h2></div></div>
                    @if($categories->isNotEmpty())
                        <div class="grid-category-grid">
                            @foreach($categories as $category)
                                <a class="grid-category-card" href="{{ route('menu', ['category' => $category->id]) }}">
                                    <span class="grid-category-card-content">
                                        <h3>{{ $category->name }}</h3>
                                        <span class="grid-category-description">{{ $category->children->isNotEmpty() ? 'Découvrez nos sous-catégories' : 'Voir les produits disponibles' }}</span>
                                        <span class="category-arrow">Explorer →</span>
                                    </span>
                                    @if($category->image_path)
                                        <img class="grid-category-image" src="{{ asset('storage/'.$category->image_path) }}" alt="" loading="lazy">
                                    @else
                                        <span class="grid-category-image grid-category-image-placeholder" aria-hidden="true">{{ mb_substr($category->name, 0, 1) }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="grid-menu-empty">Les catégories seront bientôt disponibles.</div>
                    @endif
                </section>
            @elseif($showGridSubcategories)
                <section class="grid-menu-section" aria-labelledby="subcategory-heading">
                    <a class="grid-back-link" href="{{ route('menu') }}">← Toutes les catégories</a>
                    <div class="grid-subcategory-container">
                        <div class="grid-menu-heading"><div><div class="eyebrow">Catégorie</div><h2 id="subcategory-heading">{{ $selectedParentCategory->name }}</h2></div></div>
                        <div class="grid-category-grid">
                            @foreach($selectedParentCategory->children as $child)
                                <a class="grid-category-card" href="{{ route('menu', ['category' => $child->id]) }}">
                                    <span class="grid-category-card-content">
                                        <h3>{{ $child->name }}</h3>
                                        <span class="grid-category-description">Voir les produits disponibles</span>
                                        <span class="category-arrow">Découvrir →</span>
                                    </span>
                                    @if($child->image_path)
                                        <img class="grid-category-image" src="{{ asset('storage/'.$child->image_path) }}" alt="" loading="lazy">
                                    @else
                                        <span class="grid-category-image grid-category-image-placeholder" aria-hidden="true">{{ mb_substr($child->name, 0, 1) }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>
            @else
                @if($selectedCategory && $search === '')
                    <a class="grid-back-link" href="{{ route('menu', $selectedParentCategory?->id !== $selectedCategory ? ['category' => $expandedCategoryId] : []) }}">← {{ $selectedParentCategory?->id !== $selectedCategory ? $selectedParentCategory?->name : 'Toutes les catégories' }}</a>
                @endif
                <section class="grid-product-section" aria-labelledby="menu-heading">
                    <div class="section-heading">
                        <div><div class="eyebrow">À découvrir</div><h2 id="menu-heading">Notre carte</h2><p>{{ $products->count() }} produit{{ $products->count() > 1 ? 's' : '' }} disponible{{ $products->count() > 1 ? 's' : '' }}</p></div>
                    </div>
                    <div id="menu-results">
                        @include('menu.results')
                    </div>
                </section>
            @endif
        @else
            <section class="hero">
                <div class="eyebrow">{{ $company->activity_type }}</div>
                <h1>{{ $company->name }}</h1>
                <p>{{ $company->slogan }}</p>
                <div class="hero-meta">
                    @if($company->address)<span>⌖ {{ $company->address }}</span>@endif
                    @if($company->menu_tagline)<span>{{ $company->menu_tagline }}</span>@endif
                </div>
            </section>

            <section aria-labelledby="menu-heading">
                <div class="section-heading">
                    <div><div class="eyebrow">À découvrir</div><h2 id="menu-heading">Notre carte</h2><p id="result-count" aria-live="polite">{{ $products->count() }} produit{{ $products->count() > 1 ? 's' : '' }} disponible{{ $products->count() > 1 ? 's' : '' }}</p></div>
                    <div class="heading-actions">
                        <button class="button button-outline button-small" id="toggle-product-view" type="button">Afficher en cartes</button>
                    </div>
                </div>
                <nav class="filters" aria-label="Filtrer par catégorie">
                    <a class="chip {{ !$selectedCategory ? 'active' : '' }}" data-menu-filter data-category-id="" href="{{ route('menu', ['q' => $search ?: null]) }}">Tout</a>
                    @foreach($categories as $category)
                        <a class="chip {{ $selectedCategory === $category->id ? 'active' : '' }}" data-menu-filter data-category-id="{{ $category->id }}" href="{{ route('menu', ['category' => $category->id, 'q' => $search ?: null]) }}">{{ $category->name }}</a>
                        @if($category->children->isNotEmpty())
                            <div class="subcategory-filters" data-subcategory-group="{{ $category->id }}" aria-label="Sous-catégories de {{ $category->name }}" @if($expandedCategoryId !== $category->id) hidden @endif>
                                @foreach($category->children as $child)
                                    <a class="chip {{ $selectedCategory === $child->id ? 'active' : '' }}" data-menu-filter data-category-id="{{ $child->id }}" data-parent-category="{{ $category->id }}" href="{{ route('menu', ['category' => $child->id, 'q' => $search ?: null]) }}">↳ {{ $child->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </nav>
                <form class="search-form" id="menu-search" method="GET" action="{{ route('menu') }}" role="search">
                    <input class="field" id="menu-search-input" type="search" name="q" value="{{ $search }}" placeholder="Rechercher un produit…" aria-label="Rechercher un produit">
                </form>

                <div id="menu-results">
                    @include('menu.results')
                </div>
            </section>
        @endif
    </main>
    @if($company->menu_layout !== 'category_grid')
    <script>
        (() => {
            const form = document.getElementById('menu-search');
            const input = document.getElementById('menu-search-input');
            const results = document.getElementById('menu-results');
            const count = document.getElementById('result-count');
            const filters = document.querySelector('.filters');
            const toggleView = document.getElementById('toggle-product-view');
            let cardView = false;
            let timer;
            let activeRequest = null;

            const applyProductView = () => {
                const productGrid = results.querySelector('.product-grid');
                if (productGrid) productGrid.classList.toggle('is-list', !cardView);
                toggleView.textContent = cardView ? 'Afficher en liste' : 'Afficher en cartes';
            };

            const applyCategoryState = (selectedCategory) => {
                const selectedLink = filters.querySelector(`[data-category-id="${selectedCategory || ''}"]`);
                const expandedCategory = selectedLink?.dataset.parentCategory || selectedCategory;

                filters.querySelectorAll('[data-menu-filter]').forEach((link) => {
                    link.classList.toggle('active', link.dataset.categoryId === (selectedCategory || ''));
                });
                filters.querySelectorAll('[data-subcategory-group]').forEach((group) => {
                    group.hidden = group.dataset.subcategoryGroup !== expandedCategory;
                });
            };

            const filterUrl = (categoryUrl = window.location.href) => {
                const url = new URL(categoryUrl, window.location.origin);
                const category = url.searchParams.get('category');
                const query = input.value.trim();
                url.search = '';
                if (category) url.searchParams.set('category', category);
                if (query) url.searchParams.set('q', query);
                return url;
            };

            const updateResults = async (url, historyMode = 'replace') => {
                activeRequest?.abort();
                const request = new AbortController();
                activeRequest = request;
                results.setAttribute('aria-busy', 'true');
                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                        signal: request.signal,
                    });
                    if (request !== activeRequest) return;
                    if (!response.ok) throw new Error('Impossible de charger les produits.');
                    const html = await response.text();
                    if (request !== activeRequest) return;
                    const fragment = document.createElement('div');
                    fragment.innerHTML = html;
                    const nextResults = fragment.querySelector('#menu-results-content');
                    if (!nextResults) throw new Error('La réponse du menu est invalide.');

                    results.innerHTML = nextResults.innerHTML;
                    applyProductView();
                    count.textContent = nextResults.dataset.countLabel;
                    applyCategoryState(url.searchParams.get('category'));
                    if (historyMode === 'push') {
                        window.history.pushState({}, '', url);
                    } else if (historyMode === 'replace') {
                        window.history.replaceState({}, '', url);
                    }
                } catch (error) {
                    if (error.name !== 'AbortError' && request === activeRequest) {
                        console.error('Échec de la recherche du menu.', error);
                        results.innerHTML = '<p class="errors" role="alert">La recherche a échoué. Veuillez réessayer.</p>';
                    }
                } finally {
                    if (request === activeRequest) results.removeAttribute('aria-busy');
                }
            };

            input.addEventListener('input', () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(() => updateResults(filterUrl()), 150);
            });
            toggleView.addEventListener('click', () => {
                cardView = !cardView;
                applyProductView();
            });
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                window.clearTimeout(timer);
                updateResults(filterUrl());
            });
            filters.addEventListener('click', (event) => {
                const link = event.target.closest('[data-menu-filter]');
                if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                event.preventDefault();
                window.clearTimeout(timer);
                updateResults(filterUrl(link.href), 'push');
            });
            window.addEventListener('popstate', () => {
                const url = new URL(window.location.href);
                input.value = url.searchParams.get('q') || '';
                applyCategoryState(url.searchParams.get('category'));
                updateResults(url, 'none');
            });
        })();
    </script>
    @endif
@endsection
