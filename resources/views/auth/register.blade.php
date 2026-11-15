@extends('layouts.app')

@section('title', 'Inscription - Aviatrax Studio')

@section('content')
<div class="login-page">
    <div class="login-panel">
        <div class="brand-band">
            <div class="brand-block brand-block--aviator">
                <img src="{{ asset('logo.jpg') }}" alt="Aviator Music" class="brand-logo brand-logo--aviator">
            </div>
            <div class="brand-separator">×</div>
            <div class="brand-block brand-block--maisha">
                <img src="{{ asset('logoMS.jpg') }}" alt="Maisha Sound" class="brand-logo brand-logo--maisha">
            </div>
        </div>

        <h2 class="login-title">Inscription</h2>

        <form class="login-form" action="{{ route('register') }}" method="POST">
            @csrf

            <div class="field-group">
                <label for="name">Nom</label>
                <input id="name" name="name" type="text" required autofocus value="{{ old('name') }}" placeholder="Votre nom">
            </div>

            <div class="field-group">
                <label for="email">Adresse e-mail</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="name@gmail.com">
            </div>

            <div class="field-group">
                <label for="telephone">Téléphone</label>
                <input id="telephone" name="telephone" type="tel" required value="{{ old('telephone') }}" placeholder="+243 ...">
            </div>

            <input type="hidden" name="role" value="artiste">

            <div class="field-group">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required placeholder="••••••••">
            </div>

            <div class="field-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="submit-btn">S'inscrire</button>

            <div class="register-link-wrap">
                <a href="{{ route('login') }}">Déjà un compte ? Se connecter</a>
            </div>
        </form>
    </div>
</div>

<style>
    .login-page {
        min-height: calc(100vh - 3rem);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem 0.75rem;
        background: #020b14;
    }

    .login-panel {
        width: min(100%, 32rem);
        padding: 1.15rem 1.1rem 1.35rem;
        border-radius: 1.8rem;
        border: 1px solid rgba(75, 174, 255, 0.45);
        background: linear-gradient(180deg, rgba(12, 27, 38, 0.98), rgba(11, 28, 39, 0.98));
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), inset 0 0 0 1px rgba(114, 176, 255, 0.14);
    }

    .brand-band {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 1.15rem 0.8rem;
        border-radius: 1.35rem;
        background: linear-gradient(90deg, rgba(31, 17, 8, 0.95), rgba(58, 34, 15, 0.92), rgba(14, 22, 29, 0.96));
        overflow: hidden;
        border: 1px solid rgba(251, 146, 60, 0.45);
        box-shadow: inset 0 0 20px rgba(251, 146, 60, 0.1), 0 0 18px rgba(14, 165, 233, 0.15);
    }

    .brand-band::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, rgba(251, 146, 60, 0.25), transparent 48%);
        filter: blur(20px);
        animation: brandGlow 4.8s ease-in-out infinite alternate;
    }

    .brand-block {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 8.2rem;
        height: 5.2rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2), 0 0 16px rgba(251, 146, 60, 0.2);
        animation: brandFloat 4.5s ease-in-out infinite;
    }

    .brand-block--maisha {
        animation-delay: 0.4s;
        background: rgba(255, 255, 255, 0.96);
    }

    .brand-block::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 1rem;
        background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.5), transparent 70%);
        transform: translateX(-60%);
        opacity: 0;
        animation: brandSweep 3.4s ease-in-out infinite;
    }

    .brand-separator {
        position: relative;
        z-index: 1;
        font-size: clamp(1.5rem, 3vw, 2.25rem);
        line-height: 1;
        font-weight: 900;
        color: rgba(251, 146, 60, 0.95);
        text-shadow: 0 0 18px rgba(251, 146, 60, 0.7);
        animation: brandPulse 3s ease-in-out infinite alternate;
    }

    .brand-logo {
        position: relative;
        z-index: 1;
        max-width: 80%;
        max-height: 80%;
        object-fit: contain;
        filter: drop-shadow(0 0 10px rgba(251, 146, 60, 0.15));
    }

    .brand-logo--maisha {
        background: rgba(255, 255, 255, 0.82);
        border-radius: 0.7rem;
        padding: 0.35rem;
    }

    .login-title {
        margin: 1.5rem 0 1.2rem;
        text-align: center;
        font-size: clamp(2.1rem, 4vw, 3rem);
        line-height: 1.1;
        letter-spacing: -0.05em;
        font-weight: 900;
        color: #5bbcff;
        text-shadow: 0 0 18px rgba(91, 188, 255, 0.25);
    }

    .login-form {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .field-group label {
        font-size: 1rem;
        color: #dfeffc;
        font-weight: 600;
        padding-left: 0.15rem;
    }

    .field-group input,
    .field-group select {
        width: 100%;
        border-radius: 0.9rem;
        border: 1px solid rgba(91, 188, 255, 0.72);
        background: rgba(7, 17, 24, 0.85);
        color: #f8fbff;
        padding: 0.9rem 0.95rem;
        font-size: 1rem;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .field-group input::placeholder {
        color: rgba(164, 180, 196, 0.8);
    }

    .field-group input:focus,
    .field-group select:focus {
        border-color: rgba(251, 146, 60, 0.9);
        box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.18);
    }

    .submit-btn {
        width: 100%;
        border: none;
        border-radius: 0.9rem;
        background: linear-gradient(180deg, #f5a33e 0%, #f28c1d 100%);
        color: #111827;
        font-size: 1.1rem;
        font-weight: 800;
        padding: 0.9rem 1rem;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(242, 140, 29, 0.34);
        transition: transform 0.15s ease, filter 0.15s ease;
    }

    .submit-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .register-link-wrap {
        text-align: center;
        margin-top: 0.2rem;
    }

    .register-link-wrap a {
        font-size: 0.95rem;
        color: #dfeffc;
        text-decoration: none;
        font-weight: 500;
    }

    .register-link-wrap a:hover {
        color: #d9f1ff;
        text-decoration: underline;
    }

    @keyframes brandFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-6px) scale(1.02); }
    }

    @keyframes brandPulse {
        0% { transform: scale(0.98); opacity: 0.9; }
        100% { transform: scale(1.05); opacity: 1; }
    }

    @keyframes brandGlow {
        0% { transform: translateX(-8%) scale(0.96); opacity: 0.7; }
        100% { transform: translateX(8%) scale(1.08); opacity: 1; }
    }

    @keyframes brandSweep {
        0% { transform: translateX(-60%); opacity: 0; }
        24% { opacity: 0.7; }
        100% { transform: translateX(80%); opacity: 0; }
    }
</style>
@endsection