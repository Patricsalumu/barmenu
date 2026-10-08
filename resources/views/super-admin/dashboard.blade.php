@extends('layouts.app')

@section('title', 'Back-office')

@section('content')
    <main class="admin-wrap">
        <div class="admin-title">
            <div>
                <div class="eyebrow">Super admin</div>
                <h1>Gestion des établissements</h1>
            </div>
            <form method="POST" action="{{ route('super-admin.logout') }}">
                @csrf
                <button class="button button-outline" type="submit">Déconnexion</button>
            </form>
        </div>

        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        <div class="admin-grid">
            <section class="panel">
                <h2>Créer une entreprise</h2>
                <p class="panel-intro">Le sous-domaine devient l’URL de l’établissement.</p>
                <form method="POST" action="{{ route('super-admin.companies.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group full">
                            <label for="company-name">Nom</label>
                            <input class="field" id="company-name" name="name" value="{{ old('name') }}" placeholder="Ex. Le Comptoir" required>
                        </div>
                        <div class="form-group">
                            <label for="company-subdomain">Sous-domaine</label>
                            <input class="field" id="company-subdomain" name="subdomain" value="{{ old('subdomain') }}" placeholder="ex. casabrisa" required>
                            <small>Sans espaces, sans .barmenu.site. Exemple : casabrisa</small>
                        </div>
                        <div class="form-group">
                            <label for="company-activity">Type d’activité</label>
                            <input class="field" id="company-activity" name="activity_type" value="{{ old('activity_type') }}" placeholder="Ex. Bar · Restaurant" required>
                        </div>
                        <div class="form-group">
                            <label for="company-code">Code propriétaire</label>
                            <input class="field" id="company-code" name="owner_code" value="{{ old('owner_code') }}" placeholder="Ex. 1234" required>
                        </div>
                        <div class="form-group">
                            <label for="company-color">Couleur du thème</label>
                            <input class="field" id="company-color" type="color" name="theme_color" value="{{ old('theme_color', '#c83b32') }}" style="height:44px;padding:5px">
                        </div>
                        <div class="form-group">
                            <label for="company-currency">Devise</label>
                            <select class="select" id="company-currency" name="currency">
                                <option value="F" @selected(old('currency', 'F') === 'F')>F</option>
                                <option value="$" @selected(old('currency', 'F') === '$')>$</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="company-rate">Taux de conversion</label>
                            <input class="field" id="company-rate" type="number" name="exchange_rate" min="0.01" step="0.01" value="{{ old('exchange_rate', 600) }}" required>
                        </div>
                        <div class="form-group full">
                            <label for="company-slogan">Slogan</label>
                            <input class="field" id="company-slogan" name="slogan" value="{{ old('slogan') }}" placeholder="Optionnel">
                        </div>
                        <div class="form-group full">
                            <label for="company-menu-tagline">Texte d’accroche de la carte</label>
                            <input class="field" id="company-menu-tagline" name="menu_tagline" value="{{ old('menu_tagline', '✦ Une carte préparée avec passion') }}" maxlength="180">
                        </div>
                        <div class="form-group full">
                            <label for="company-address">Adresse</label>
                            <input class="field" id="company-address" name="address" value="{{ old('address') }}" placeholder="Optionnel">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="button" type="submit">Créer l’entreprise</button>
                    </div>
                </form>
            </section>

            <section class="panel">
                <h2>Entreprises existantes</h2>
                <p class="panel-intro">Chaque establishment est accessible via son sous-domaine.</p>
                <div class="admin-list">
                    @forelse($companies as $company)
                        @php
                            $currentHost = request()->getHost();
                            $isLocalHost = in_array($currentHost, ['localhost', '127.0.0.1', '[::1]'], true)
                                || str_ends_with($currentHost, '.localhost');
                            $tenantDomain = $isLocalHost ? 'localhost' : 'barmenu.site';
                            $tenantPort = request()->getPort();
                            $portSuffix = in_array($tenantPort, [80, 443], true) ? '' : ':'.$tenantPort;
                            $tenantUrl = request()->getScheme().'://'.$company->subdomain.'.'.$tenantDomain.$portSuffix.'/proprietaire/connexion';
                        @endphp
                        <div class="list-row">
                            <div>
                                <div class="row-title">{{ $company->name }}</div>
                                <div class="row-meta">{{ $tenantUrl }} · code propriétaire : {{ $company->owner_code ? '********' : 'non défini' }}</div>
                            </div>
                            <a class="button button-outline button-small" href="{{ $tenantUrl }}" target="_blank" rel="noopener noreferrer">Ouvrir</a>
                        </div>
                    @empty
                        <div class="row-meta">Aucune entreprise créée pour le moment.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
@endsection
