@extends('layouts.app')

@section('title', 'Super admin')

@section('content')
    <main class="login-card">
        <div class="eyebrow">Administration</div>
        <h1>Super admin</h1>
        <p>Créez une entreprise, définissez son sous-domaine et son code propriétaire.</p>
        <form method="POST" action="{{ route('super-admin.authenticate') }}">
            @csrf
            <label class="form-group" for="password"><span>Mot de passe</span></label>
            <input class="field" id="password" type="password" name="password" inputmode="numeric" autocomplete="current-password" required autofocus>
            @error('password')<div class="errors">{{ $message }}</div>@enderror
            <button class="button" type="submit" style="width:100%;margin-top:8px">Accéder au back-office →</button>
        </form>
        <p style="margin:18px 0 0;font-size:11px">Mot de passe actuel : <strong>123456</strong>. Vous pourrez le modifier plus tard dans le code ou l’environnement.</p>
    </main>
@endsection
