@extends('layouts.app')

@section('title', $company->name)

@section('content')
    <main>
        <section class="hero">
            <div class="eyebrow">{{ $company->activity_type }}</div>
            <h1>{{ $company->name }}</h1>
            <p>{{ $company->slogan }}</p>
            <div class="hero-meta">
                @if($company->address)<span>⌖ {{ $company->address }}</span>@endif
                <span>✦ Une carte préparée avec passion</span>
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
                <a class="chip {{ !$selectedCategory ? 'active' : '' }}" data-menu-filter href="{{ route('menu', ['q' => $search ?: null]) }}">Tout</a>
                @foreach($categories as $category)
                    <a class="chip {{ $selectedCategory === $category->id ? 'active' : '' }}" data-menu-filter href="{{ route('menu', ['category' => $category->id, 'q' => $search ?: null]) }}">{{ $category->name }}</a>
                    @foreach($category->children as $child)
                        <a class="chip {{ $selectedCategory === $child->id ? 'active' : '' }}" data-menu-filter href="{{ route('menu', ['category' => $child->id, 'q' => $search ?: null]) }}">{{ $child->name }}</a>
                    @endforeach
                @endforeach
            </nav>
            <form class="search-form" id="menu-search" method="GET" action="{{ route('menu') }}" role="search">
                <input class="field" id="menu-search-input" type="search" name="q" value="{{ $search }}" placeholder="Rechercher un produit…" aria-label="Rechercher un produit">
            </form>

            <div id="menu-results">
                @include('menu.results')
            </div>
        </section>
    </main>
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
                    filters.querySelectorAll('[data-menu-filter]').forEach((link) => {
                        const linkCategory = new URL(link.href).searchParams.get('category');
                        const selectedCategory = url.searchParams.get('category');
                        link.classList.toggle('active', linkCategory === selectedCategory);
                    });
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
                updateResults(url, 'none');
            });
        })();
    </script>
@endsection
