@extends('layouts.app')

@section('title', 'Gestion')

@section('content')
    <main class="admin-wrap">
        <div class="admin-title">
            <div><div class="eyebrow">Espace propriétaire</div><h1>Votre établissement</h1><p>Gérez les informations et la carte de {{ $company->name }}.</p></div>
            <a class="button button-outline" href="{{ route('menu') }}">Ouvrir le menu public ↗</a>
        </div>
        @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif

        <div class="admin-grid">
            <section class="panel panel-wide">
                <h2>Informations du bar</h2><p class="panel-intro">Ces informations sont visibles sur votre menu public.</p>
                <form method="POST" action="{{ route('owner.company.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="form-grid">
                        <div class="form-group"><label for="company-name">Nom de l’établissement</label><input class="field" id="company-name" name="name" value="{{ old('name', $company->name) }}" required></div>
                        <div class="form-group"><label for="company-slogan">Slogan</label><input class="field" id="company-slogan" name="slogan" value="{{ old('slogan', $company->slogan) }}"></div>
                        <div class="form-group"><label for="company-address">Adresse</label><input class="field" id="company-address" name="address" value="{{ old('address', $company->address) }}"></div>
                        <div class="form-group"><label for="company-logo">Logo</label><input class="field" id="company-logo" type="file" name="logo" accept="image/*"><small>Image carrée conseillée · 2 Mo maximum</small></div>
                        <div class="form-group"><label for="theme-color">Couleur du thème</label><input class="field" id="theme-color" type="color" name="theme_color" value="{{ old('theme_color', $company->theme_color) }}" style="height:44px;padding:5px"></div>
                        <div class="form-group"><label for="currency">Devise des prix</label><select class="select" id="currency" name="currency"><option value="F" @selected(old('currency', $company->currency) === 'F')>F</option><option value="$" @selected(old('currency', $company->currency) === '$')>$</option></select></div>
                        <div class="form-group"><label for="exchange-rate">Taux de conversion</label><input class="field" id="exchange-rate" type="number" name="exchange_rate" min="0.01" step="0.01" value="{{ old('exchange_rate', $company->exchange_rate) }}" required><small>Nombre de F pour 1 $</small></div>
                        <div class="form-group"><label for="owner-code">Nouveau code propriétaire</label><input class="field" id="owner-code" type="password" name="owner_code" minlength="4" placeholder="Laisser vide pour conserver le code actuel"><small>4 caractères minimum</small></div>
                    </div>
                    <div class="form-actions"><button class="button" type="submit">Enregistrer les informations</button></div>
                </form>
            </section>

            <section class="panel">
                <h2>Catégories</h2><p class="panel-intro">Créez des catégories seules ou des sous-catégories.</p>
                <form method="POST" action="{{ route('owner.categories.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group"><label for="category-name">Nom</label><input class="field" id="category-name" name="name" placeholder="Ex. Cocktails" required></div>
                        <div class="form-group"><label for="parent-category">Sous-catégorie de</label><select class="select" id="parent-category" name="parent_id"><option value="">Aucune (catégorie principale)</option>@foreach($categories->whereNull('parent_id') as $parent)<option value="{{ $parent->id }}">{{ $parent->name }}</option>@endforeach</select></div>
                    </div>
                    <div class="form-actions"><button class="button button-small" type="submit">+ Ajouter</button></div>
                </form>
                <div class="admin-list" style="margin-top:17px">
                    @forelse($categories->whereNull('parent_id') as $category)
                        <div class="category-admin">
                            <div class="list-row">
                                <form class="inline-edit" method="POST" action="{{ route('owner.categories.update', $category) }}">@csrf @method('PUT')<input class="field" name="name" value="{{ $category->name }}" aria-label="Nom de {{ $category->name }}"><button class="button button-outline button-small" type="submit">OK</button></form>
                                <form method="POST" action="{{ route('owner.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette catégorie et tous ses produits ?')">@csrf @method('DELETE')<button class="button button-small danger" type="submit">Supprimer</button></form>
                            </div>
                            @foreach($category->children as $child)
                                <div class="category-admin-child">
                                    <div class="list-row">
                                        <form class="inline-edit" method="POST" action="{{ route('owner.categories.update', $child) }}">@csrf @method('PUT')<input class="field" name="name" value="{{ $child->name }}" aria-label="Nom de {{ $child->name }}"><button class="button button-outline button-small" type="submit">OK</button></form>
                                        <form method="POST" action="{{ route('owner.categories.destroy', $child) }}" onsubmit="return confirm('Supprimer cette sous-catégorie et ses produits ?')">@csrf @method('DELETE')<button class="button button-small danger" type="submit">×</button></form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="row-meta">Aucune catégorie pour le moment.</div>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <h2>Ajouter un produit</h2><p class="panel-intro">Le prix sera affiché en {{ $company->currency }} sur votre menu.</p>
                <form method="POST" action="{{ route('owner.products.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group full"><label for="product-name">Nom du produit</label><input class="field" id="product-name" name="name" placeholder="Ex. Mojito maison" required></div>
                        <div class="form-group"><label for="product-category">Catégorie</label><select class="select" id="product-category" name="category_id" required><option value="">Choisir…</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->parent?->name ? $category->parent->name.' · ' : '' }}{{ $category->name }}</option>@endforeach</select></div>
                        <div class="form-group"><label for="product-price">Prix ({{ $company->currency }})</label><input class="field" id="product-price" name="price" type="number" min="0" step="0.01" placeholder="0" required></div>
                        <div class="form-group full"><label for="product-description">Description</label><textarea class="textarea" id="product-description" name="description" placeholder="Quelques mots pour donner envie…"></textarea></div>
                    </div>
                    <div class="form-actions"><button class="button" type="submit">+ Ajouter à la carte</button></div>
                </form>
            </section>

            <section class="panel panel-wide">
                <h2>Votre carte</h2><p class="panel-intro">{{ $company->products->count() }} produit{{ $company->products->count() > 1 ? 's' : '' }} au total. Modifiez les informations ou masquez un produit temporairement.</p>
                <div class="admin-list">
                    @forelse($company->products->sortBy('name') as $product)
                        <div class="list-row">
                            <form class="inline-product" method="POST" action="{{ route('owner.products.update', $product) }}">
                                @csrf @method('PUT')
                                <input class="field" name="name" value="{{ $product->name }}" aria-label="Nom du produit" required>
                                <select class="select" name="category_id" aria-label="Catégorie">@foreach($categories as $category)<option value="{{ $category->id }}" @selected($product->category_id === $category->id)>{{ $category->parent?->name ? $category->parent->name.' · ' : '' }}{{ $category->name }}</option>@endforeach</select>
                                <input class="field" type="number" name="price" value="{{ $product->price }}" min="0" step="0.01" aria-label="Prix" required>
                                <label class="row-meta"><input type="checkbox" name="is_available" value="1" @checked($product->is_available)> Visible</label>
                                <button class="button button-outline button-small" type="submit">Enregistrer</button>
                            </form>
                            <div class="row-meta">{{ $product->description }}</div>
                            <form method="POST" action="{{ route('owner.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?')">@csrf @method('DELETE')<button class="button button-small danger" type="submit">Supprimer</button></form>
                        </div>
                    @empty
                        <div class="row-meta">Aucun produit. Ajoutez-en un à l’aide du formulaire ci-dessus.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
@endsection
