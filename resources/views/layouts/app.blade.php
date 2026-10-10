@php
    $company = $company ?? null;
    $companyName = $company?->name ?? 'BarMenu';
    $companyThemeColor = $company?->theme_color ?? '#c83b32';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ $companyThemeColor }}">
    <title>@yield('title', $companyName) — Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--accent:{{ $companyThemeColor }};--ink:#f8f5f0;--muted:#a5a09c;--surface:#171615;--line:rgba(255,255,255,.1);--serif:'Playfair Display',Georgia,serif;--sans:'DM Sans',Arial,sans-serif}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:#111;color:var(--ink);font-family:var(--sans);font-size:15px;line-height:1.55}a{color:inherit;text-decoration:none}button,input,select,textarea{font:inherit}button{cursor:pointer}.shell{width:min(1160px,calc(100% - 48px));margin:auto}.topbar{height:78px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line)}.brand{display:flex;align-items:center;gap:12px;font-weight:700;letter-spacing:.02em}.brand-mark{width:38px;height:38px;border-radius:50%;display:grid;place-items:center;background:var(--accent);font-family:var(--serif);font-size:20px}.brand-logo{width:42px;height:42px;object-fit:cover;border-radius:50%}.top-actions{display:flex;align-items:center;gap:18px}.subtle-link{color:var(--muted);font-size:13px}.button{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:7px;padding:11px 17px;background:var(--accent);color:white;font-weight:700;font-size:13px;transition:transform .18s,filter .18s}.button:hover{filter:brightness(1.12);transform:translateY(-1px)}.button-outline{background:transparent;border:1px solid var(--line);color:var(--ink)}.button-small{padding:8px 12px}.hero{position:relative;overflow:hidden;margin:28px 0 35px;padding:64px 58px;border:1px solid var(--line);border-radius:14px;background:radial-gradient(ellipse at 85% 10%,color-mix(in srgb,var(--accent) 35%,transparent),transparent 42%),linear-gradient(120deg,#1c1a19,#151413 72%)}.hero:after{content:"";position:absolute;width:260px;height:260px;right:10%;top:-135px;border:1px solid rgba(255,255,255,.12);border-radius:50%;box-shadow:0 0 0 30px rgba(255,255,255,.025),0 0 0 60px rgba(255,255,255,.018)}.eyebrow{color:var(--accent);font-weight:700;font-size:11px;letter-spacing:.18em;text-transform:uppercase}.hero h1{position:relative;z-index:1;max-width:700px;margin:12px 0 9px;font:600 clamp(38px,6vw,68px)/1.1 var(--serif);letter-spacing:-.035em}.hero p{position:relative;z-index:1;color:#c0bbb5;margin:0}.hero-meta{position:relative;z-index:1;display:flex;gap:20px;flex-wrap:wrap;margin-top:26px;color:#d3cec8;font-size:13px}.hero-meta span{display:flex;align-items:center;gap:7px}.section-heading{display:flex;justify-content:space-between;align-items:end;gap:18px;margin:28px 0 16px}.section-heading h2{font:600 27px var(--serif);margin:0}.section-heading p{margin:4px 0 0;color:var(--muted);font-size:13px}.filters{display:flex;align-items:center;gap:9px;flex-wrap:wrap;margin:0 0 16px}.chip{white-space:nowrap;padding:8px 14px;border:1px solid var(--line);border-radius:30px;color:#c4bfba;font-size:12px;transition:.2s}.chip:hover,.chip.active{background:var(--accent);border-color:var(--accent);color:white}.search-form{display:flex;gap:8px;margin:0 0 30px}.field,.select,.textarea{width:100%;padding:11px 13px;border-radius:7px;border:1px solid var(--line);outline:none;background:#111;color:var(--ink)}.field:focus,.select:focus,.textarea:focus{border-color:var(--accent)}.search-form .field{max-width:340px}.product-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.product-card{position:relative;display:flex;flex-direction:column;min-height:156px;padding:21px;background:linear-gradient(145deg,#1c1a19,#171615);border:1px solid var(--line);border-radius:10px;transition:transform .2s,border-color .2s}.product-card:hover{transform:translateY(-3px);border-color:color-mix(in srgb,var(--accent) 55%,transparent)}.product-top{display:flex;justify-content:space-between;align-items:start;gap:12px}.product-card h3{font:600 19px var(--serif);margin:0}.product-card p{font-size:13px;color:var(--muted);margin:9px 0 18px}.category-label{font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--accent);font-weight:700}.price{white-space:nowrap;color:#f4d4ae;font-weight:700;font-size:14px}.empty{grid-column:1/-1;padding:50px 20px;text-align:center;border:1px dashed var(--line);border-radius:10px;color:var(--muted)}.footer{margin-top:65px;padding:24px 0;border-top:1px solid var(--line);display:flex;justify-content:space-between;color:var(--muted);font-size:12px}.footer a:hover{color:var(--ink)}
        .section-heading{align-items:center}.heading-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}.product-grid.is-list{grid-template-columns:1fr;gap:0}.product-grid.is-list .product-card{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;min-height:0;padding:15px 18px;border:0;border-bottom:1px solid var(--line);border-radius:0;background:transparent}.product-grid.is-list .product-card:first-child{border-top:1px solid var(--line)}.product-grid.is-list .product-card:hover{transform:none;background:rgba(255,255,255,.035)}.product-grid.is-list .product-top{grid-column:1/-1;align-items:center}.product-grid.is-list .product-card p{grid-column:1/-1;margin:4px 0 0}.product-grid.is-list .empty{grid-column:1/-1}
        .admin-wrap{padding:34px 0 65px}.admin-title{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:25px}.admin-title h1{margin:2px 0;font:600 34px var(--serif)}.admin-title p{margin:0;color:var(--muted);font-size:13px}.admin-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start}.panel{padding:22px;background:#191817;border:1px solid var(--line);border-radius:10px}.panel-wide{grid-column:1/-1}.panel h2{font:600 21px var(--serif);margin:0 0 5px}.panel-intro{margin:0 0 18px;color:var(--muted);font-size:12px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px}.form-group{display:flex;flex-direction:column;gap:6px}.form-group.full{grid-column:1/-1}.form-group label{font-size:11px;font-weight:700;color:#d1ccc5}.form-group small{color:var(--muted);font-size:10px}.textarea{min-height:76px;resize:vertical}.form-actions{margin-top:15px}.admin-list{display:flex;flex-direction:column;gap:9px}.list-row{display:grid;grid-template-columns:1fr auto;gap:12px;align-items:center;padding:12px;border:1px solid var(--line);border-radius:8px}.row-title{font-weight:600;font-size:13px}.row-meta{color:var(--muted);font-size:11px}.inline-edit{display:flex;align-items:center;gap:8px}.inline-edit .field{min-width:100px;padding:8px 10px;font-size:12px}.danger{color:#f08f87;background:transparent;border:1px solid rgba(240,143,135,.3)}.danger:hover{background:rgba(240,143,135,.1)}.flash{margin-bottom:16px;padding:12px 15px;border:1px solid rgba(82,180,130,.35);border-radius:7px;background:rgba(82,180,130,.08);color:#9ce0b5;font-size:13px}.errors{margin:12px 0;padding:11px 14px;border:1px solid rgba(240,143,135,.35);border-radius:7px;color:#f3a39c;font-size:12px}.category-admin{padding:12px 0;border-bottom:1px solid var(--line)}.category-admin:last-child{border-bottom:0}.category-admin-child{padding-left:23px}.inline-product{display:grid;grid-template-columns:1.3fr 1.3fr .7fr auto auto;gap:8px;align-items:center}.login-card{width:min(430px,100%);margin:72px auto;padding:32px;background:#191817;border:1px solid var(--line);border-radius:13px}.login-card h1{font:600 31px var(--serif);margin:5px 0}.login-card p{color:var(--muted);font-size:13px;margin:0 0 24px}.login-card .field{margin-bottom:12px}
        .category-image-field{display:flex;flex-direction:column;gap:4px;min-width:130px;color:var(--muted);font-size:10px}.category-image-field .field{width:100%;padding:6px;font-size:10px}
        .subcategory-filters{display:flex;align-items:center;gap:9px;flex-wrap:wrap;margin:4px 0 8px 16px;padding:8px 12px;border-left:2px solid var(--accent);background:rgba(255,255,255,.025);border-radius:0 8px 8px 0}.subcategory-filters[hidden]{display:none}
        @media(max-width:800px){.product-grid:not(.is-list){grid-template-columns:repeat(2,minmax(0,1fr))}.admin-grid{grid-template-columns:1fr}.panel-wide{grid-column:auto}.hero{padding:48px 32px}.inline-product{grid-template-columns:1fr 1fr}.inline-product>*{min-width:0}.inline-product .field{width:100%}}@media(max-width:540px){.shell{width:min(100% - 28px,1160px)}.topbar{height:66px}.hero{margin:18px 0 28px;padding:39px 24px}.hero h1{font-size:43px}.product-grid:not(.is-list){grid-template-columns:1fr}.product-grid:not(.is-list) .product-card{min-height:130px}.product-grid.is-list .product-card{padding:13px 8px}.top-actions{gap:9px}.subtle-link{font-size:11px}.button{padding:10px 13px}.section-heading h2{font-size:24px}.admin-title{align-items:flex-start;flex-direction:column}.form-grid{grid-template-columns:1fr}.form-group.full{grid-column:auto}.list-row{grid-template-columns:1fr}.inline-edit{width:100%}.footer{gap:12px;flex-direction:column}}
    </style>
    <style>
        .grid-menu-intro{display:flex;align-items:center;gap:28px;margin:34px 0 42px;padding:36px;border:1px solid var(--line);border-radius:16px;background:radial-gradient(ellipse at 90% 0,color-mix(in srgb,var(--accent) 24%,transparent),transparent 52%),linear-gradient(135deg,#1c1a19,#151413)}
        .grid-menu-logo,.grid-menu-logo-placeholder{width:128px;height:128px;flex:0 0 128px;border-radius:18px;object-fit:cover}
        .grid-menu-logo-placeholder{display:grid;place-items:center;background:var(--accent);font:600 56px var(--serif);color:white}
        .grid-menu-intro h1{margin:5px 0 6px;font:600 clamp(32px,5vw,52px)/1.1 var(--serif);letter-spacing:-.03em}
        .grid-menu-intro p{margin:0;color:#c0bbb5}
        .grid-menu-meta{display:flex;gap:10px 22px;flex-wrap:wrap;margin-top:17px;color:#d3cec8;font-size:13px}
        .grid-whatsapp-cta{display:flex;align-items:center;gap:15px;flex-wrap:wrap;margin-top:22px;padding:15px 17px;border:1px solid color-mix(in srgb,var(--accent) 45%,transparent);border-radius:10px;background:color-mix(in srgb,var(--accent) 12%,#171615)}
        .grid-whatsapp-cta p{margin:0;color:#e7e1da;font-size:13px}
        .grid-whatsapp-cta .button{flex:0 0 auto}
        .grid-menu-section{margin-bottom:30px}
        .grid-menu-heading{display:flex;align-items:center;gap:12px;margin:0 0 18px}
        .grid-menu-heading h2{margin:0;font:600 27px var(--serif)}
        .grid-category-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:15px}
        .grid-category-card{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:145px;padding:18px;border:1px solid var(--line);border-radius:12px;background:linear-gradient(145deg,#1c1a19,#171615);transition:transform .2s,border-color .2s}
        .grid-category-card:hover{transform:translateY(-3px);border-color:color-mix(in srgb,var(--accent) 60%,transparent)}
        .grid-category-card h3{margin:0;font:600 20px var(--serif)}
        .grid-category-card-content{display:flex;flex:1;flex-direction:column;align-items:flex-start;min-width:0}
        .grid-category-description{margin:8px 0 0;color:var(--muted);font-size:12px}
        .grid-category-card .category-arrow{margin-top:auto;padding-top:18px;color:var(--accent);font-size:12px;font-weight:700}
        .grid-category-image{width:88px;height:88px;flex:0 0 88px;border-radius:10px;object-fit:cover}
        .grid-category-image-placeholder{display:grid;place-items:center;background:color-mix(in srgb,var(--accent) 20%,#171615);color:var(--accent);font:600 34px var(--serif)}
        .grid-subcategory-container{padding:23px;border:1px solid var(--line);border-radius:14px;background:#171615}
        .grid-subcategory-container .grid-category-card{min-height:118px;background:#1c1a19}
        .grid-back-link{display:inline-flex;align-items:center;gap:7px;margin-bottom:15px;color:var(--muted);font-size:12px}
        .grid-back-link:hover{color:var(--ink)}
        .grid-search{margin-bottom:28px}
        .grid-search .field{max-width:520px}
        .grid-product-section .section-heading{margin-top:0}
        .grid-menu-empty{padding:26px;text-align:center;border:1px dashed var(--line);border-radius:10px;color:var(--muted)}
        @media(max-width:800px){.grid-category-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.grid-menu-intro{padding:28px}}
        @media(max-width:540px){.grid-menu-intro{flex-direction:column;align-items:center;gap:18px;margin:20px 0 30px;padding:24px;text-align:center}.grid-menu-intro>div{width:100%}.grid-menu-logo,.grid-menu-logo-placeholder{width:96px;height:96px;flex-basis:96px;border-radius:14px}.grid-menu-logo-placeholder{font-size:44px}.grid-menu-meta{justify-content:center}.grid-whatsapp-cta{flex-direction:column;align-items:center}.grid-category-grid{grid-template-columns:1fr}.grid-category-card{min-height:120px;padding:16px}.grid-category-image{width:76px;height:76px;flex-basis:76px}.grid-subcategory-container{padding:14px}.grid-menu-heading h2{font-size:24px}.category-image-field{width:100%}}
    </style>
</head>
<body>
    <div class="shell">
        @if($company?->menu_layout !== 'category_grid' || request()->routeIs('owner.*'))
        <header class="topbar">
            <a class="brand" href="{{ route('menu') }}">
                @if($company?->logo_path)
                    <img class="brand-logo" src="{{ asset('storage/'.$company->logo_path) }}" alt="Logo {{ $company->name }}">
                @else
                    <span class="brand-mark">{{ mb_substr($companyName, 0, 1) }}</span>
                @endif
                <span>{{ $companyName }}</span>
            </a>
            @if(request()->routeIs('owner.*'))
                <nav class="top-actions">
                    <a class="subtle-link" href="{{ route('menu') }}">Voir le menu ↗</a>
                    @if(session('owner_authenticated'))
                        <form method="POST" action="{{ route('owner.logout') }}">@csrf<button class="button button-outline button-small" type="submit">Déconnexion</button></form>
                    @endif
                </nav>
            @elseif($company?->whatsapp_number)
                <a class="subtle-link" href="https://wa.me/{{ $company->whatsapp_number }}?text={{ rawurlencode('Salut '.$company->name.', je suis un client de votre espace et je vous écris concernant : ') }}" target="_blank" rel="noopener noreferrer">Une question ou une réservation ? WhatsApp ↗</a>
            @endif
        </header>
        @endif
        @if($errors->any())
            <div class="errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        @endif
        @yield('content')
        <footer class="footer">
            <span>© {{ date('Y') }} {{ $companyName }} · Fait avec soin</span>
            @if(!request()->routeIs('owner.*') && $company?->whatsapp_number)
                <a class="subtle-link" href="https://wa.me/{{ $company->whatsapp_number }}?text={{ rawurlencode('Salut '.$company->name.', je suis un client de votre espace et je vous écris concernant : ') }}" target="_blank" rel="noopener noreferrer">Une remarque, une réclamation ou une réservation ? Écrivez-nous sur WhatsApp ↗</a>
            @endif
        </footer>
    </div>
</body>
</html>
