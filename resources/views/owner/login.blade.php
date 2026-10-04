@extends('layouts.app')

@section('title', 'Accès propriétaire')

@section('content')
    <main class="login-card">
        <div class="eyebrow">Espace privé</div>
        <h1>Bonjour, {{ $company->name }}</h1>
        <p>Saisissez votre code propriétaire pour gérer votre carte et les informations de votre établissement.</p>
        <form method="POST" action="{{ route('owner.authenticate') }}">
            @csrf
            <label class="form-group" for="code"><span>Votre code d’accès</span></label>
            <input class="field" id="code" type="password" name="code" inputmode="numeric" autocomplete="current-password" required autofocus>
            @error('code')<div class="errors">{{ $message }}</div>@enderror
            <button class="button" type="submit" style="width:100%;margin-top:8px">Accéder à mon espace →</button>
        </form>
        <p style="margin:18px 0 0;font-size:11px">Code initial de démonstration : <strong>1234</strong>. Pensez à le modifier après connexion.</p>
    </main>
@endsection
