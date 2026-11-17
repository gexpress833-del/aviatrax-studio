@extends('layouts.app')

@section('title', 'Dashboard Admin - Aviatrax Studio')

@section('content')
<h1 class="text-3xl font-bold text-blue-400 mb-6">Dashboard Administrateur</h1>
<div class="space-y-6">
    <div class="overflow-hidden rounded-lg border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-orange-400 mb-4">Statistiques générales</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="rounded-lg border border-blue-500/20 bg-blue-500/10 p-4">
                    <div class="text-2xl font-bold text-blue-300">{{ $stats['total_users'] }}</div>
                    <div class="text-sm text-blue-200">Utilisateurs total</div>
                </div>
                <div class="rounded-lg border border-amber-500/20 bg-amber-500/10 p-4">
                    <div class="text-2xl font-bold text-amber-300">{{ $stats['users_en_attente'] }}</div>
                    <div class="text-sm text-amber-200">En attente de validation</div>
                </div>
                <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/10 p-4">
                    <div class="text-2xl font-bold text-emerald-300">{{ $stats['total_projets'] }}</div>
                    <div class="text-sm text-emerald-200">Projets total</div>
                </div>
                <div class="rounded-lg border border-fuchsia-500/20 bg-fuchsia-500/10 p-4">
                    <div class="text-2xl font-bold text-fuchsia-300">{{ $stats['projets_actifs'] }}</div>
                    <div class="text-sm text-fuchsia-200">Projets actifs</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Utilisateurs en attente -->
        <div class="overflow-hidden rounded-lg border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-blue-300 mb-4">Utilisateurs en attente de validation</h3>
                @if($usersEnAttente->count() > 0)
                    <div class="space-y-3">
                        @foreach($usersEnAttente as $user)
                            <div class="flex flex-col gap-3 rounded-lg border border-slate-700 bg-slate-800/80 p-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="font-medium text-slate-100">{{ $user->name }}</div>
                                    <div class="text-sm text-slate-300">{{ $user->email }}</div>
                                    <div class="text-sm text-slate-400">{{ $user->telephone }}</div>
                                </div>
                                <form method="POST" action="{{ route('admin.users.validate', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                        Valider
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-400">Aucun utilisateur en attente de validation</p>
                @endif
            </div>
        </div>

        <!-- Projets récents -->
        <div class="overflow-hidden rounded-lg border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-blue-300 mb-4">Projets récents</h3>
                @if($projetsRecents->count() > 0)
                    <div class="space-y-3">
                        @foreach($projetsRecents as $projet)
                            <div class="rounded-lg border border-slate-700 bg-slate-800/80 p-3">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-100">{{ $projet->titre }}</div>
                                        <div class="text-sm text-slate-300">{{ $projet->artiste->name }}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded text-white" style="background-color: {{ $projet->etape_color }}">
                                        {{ $projet->etape_label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-400">Aucun projet récent</p>
                @endif
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-700 bg-slate-900/90 shadow-xl shadow-slate-950/30">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-orange-400 mb-4">Actions rapides</h3>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
                <a href="{{ route('admin.users') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Gérer les utilisateurs
                </a>
                <a href="{{ route('projets.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Créer un projet
                </a>
                <a href="{{ route('admin.logs') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Voir les logs
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 