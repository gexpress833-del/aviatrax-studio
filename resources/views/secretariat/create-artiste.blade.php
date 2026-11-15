@extends('layouts.app')

@section('title', 'Créer un artiste - Aviatrax Studio')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-bold text-white">Créer un compte artiste</h1>
        <a href="{{ route('secretariat.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-800 text-slate-200 border border-slate-700 hover:bg-slate-700 transition">
            Retour au dashboard
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl shadow-slate-950/40">
        <div class="px-5 py-6 sm:p-7">
            <form action="{{ route('secretariat.artistes.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-200 mb-2">Nom complet</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-slate-400">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-200 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-slate-400">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telephone" class="block text-sm font-medium text-slate-200 mb-2">Téléphone</label>
                    <input type="tel" name="telephone" id="telephone" value="{{ old('telephone') }}" required
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-slate-400">
                    @error('telephone')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-200 mb-2">Mot de passe</label>
                    <input type="password" name="password" id="password" required
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-200 mb-2">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="border border-blue-400/30 bg-blue-500/10 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 flex-shrink-0 w-6 h-6 rounded-full bg-blue-500/20 text-blue-300 flex items-center justify-center text-sm font-bold">i</div>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-200">Information</h3>
                            <p class="text-sm text-blue-100/90 mt-1">Le compte artiste sera créé avec le statut "valide" et pourra se connecter immédiatement.</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <a href="{{ route('secretariat.dashboard') }}" class="px-5 py-2.5 rounded-lg border border-slate-600 bg-slate-800 text-slate-200 hover:bg-slate-700 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium shadow-lg shadow-blue-600/30 transition">
                        Créer l'artiste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 