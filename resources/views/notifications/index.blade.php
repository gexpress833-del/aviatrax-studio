@extends('layouts.app')

@section('title', 'Notifications - Aviatrax Studio')

@section('content')
@php
    $filterLabels = [
        'all' => 'Toutes',
        'unread' => 'Non lues',
        'read' => 'Lues',
    ];
@endphp

<div class="mx-auto max-w-5xl space-y-5 pb-8 sm:space-y-6">
    <header class="relative overflow-hidden rounded-2xl border border-slate-700/80 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950/40 p-5 sm:p-7">
        <div class="absolute -right-16 -top-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3 sm:gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-indigo-400/30 bg-indigo-500/10 text-indigo-300 sm:h-12 sm:w-12">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/></svg>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Centre d’activité</p>
                    <h1 class="mt-1 text-2xl font-bold text-white sm:text-3xl">Notifications</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ $unreadCount }} notification{{ $unreadCount > 1 ? 's' : '' }} non lue{{ $unreadCount > 1 ? 's' : '' }}</p>
                </div>
            </div>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-indigo-400/30 bg-indigo-500/10 px-4 py-2.5 text-sm font-semibold text-indigo-200 transition hover:border-indigo-300/50 hover:bg-indigo-500/20 sm:w-auto">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l3 3 6-6m-1 7l2 2 7-7M14 5l2-2 3 3"/></svg>
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>
    </header>

    <nav class="flex gap-2 overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70 p-1.5" aria-label="Filtrer les notifications">
        @foreach($filterLabels as $key => $label)
            <a href="{{ route('notifications.index', ['filter' => $key === 'all' ? null : $key]) }}" class="shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition {{ $filter === $key ? 'bg-slate-100 text-slate-950 shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70 shadow-2xl shadow-slate-950/20">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $title = $data['titre'] ?? ($data['artiste_name'] ?? 'Nouvelle notification');
                $message = $data['etape_label'] ?? ($data['artiste_email'] ?? 'Vous avez une mise à jour importante.');
                $targetUrl = isset($data['projet_id']) ? route('projets.show', $data['projet_id']) : route('dashboard');
                $isUnread = is_null($notification->read_at);
                $accent = $data['etape_color'] ?? '#818cf8';
            @endphp

            <article class="group relative border-b border-slate-800/90 last:border-b-0 {{ $isUnread ? 'bg-indigo-500/[0.045]' : 'bg-transparent' }}">
                @if($isUnread)<span class="absolute bottom-5 left-0 top-5 w-0.5 rounded-r-full bg-indigo-400"></span>@endif
                <div class="flex items-start gap-3 p-4 sm:gap-4 sm:p-5">
                    <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-700 bg-slate-950 text-sm font-bold text-white sm:h-12 sm:w-12" style="box-shadow: inset 0 0 0 3px {{ $accent }}20">
                        @if(isset($data['projet_id']))
                            <svg class="h-5 w-5" style="color: {{ $accent }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18V5l12-2v13M9 9l12-2M9 18a3 3 0 11-6 0 3 3 0 016 0zm12-2a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @else
                            <svg class="h-5 w-5 text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm13 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        @endif
                    </span>

                    <a href="{{ $targetUrl }}" data-read-url="{{ route('notifications.read', $notification->id) }}" class="min-w-0 flex-1 rounded-lg outline-none transition focus-visible:ring-2 focus-visible:ring-indigo-400">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-semibold text-white sm:text-base">{{ $title }}</h2>
                                    @if($isUnread)<span class="rounded-full border border-indigo-400/20 bg-indigo-500/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-300">Nouveau</span>@endif
                                </div>
                                <p class="mt-1.5 text-sm leading-6 text-slate-300">{{ $message }}</p>
                            </div>
                            <svg class="mt-1 h-4 w-4 shrink-0 text-slate-600 transition group-hover:translate-x-0.5 group-hover:text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                        </div>
                        <time datetime="{{ $notification->created_at->toIso8601String() }}" class="mt-3 block text-xs font-medium text-slate-500">{{ $notification->created_at->diffForHumans() }}</time>
                    </a>
                </div>
            </article>
        @empty
            <div class="px-6 py-16 text-center sm:py-20">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-slate-700 bg-slate-950 text-slate-500">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                </span>
                <h2 class="mt-4 text-lg font-semibold text-white">Aucune notification</h2>
                <p class="mt-2 text-sm text-slate-400">Vous êtes à jour pour le moment.</p>
            </div>
        @endforelse
    </section>

    @if($notifications->hasPages())
        <div class="rounded-xl border border-slate-800 bg-slate-900/60 px-3 py-3 [&_nav]:flex [&_nav]:justify-center [&_svg]:h-4 [&_svg]:w-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('a[data-read-url]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            fetch(link.dataset.readUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            }).finally(function () {
                window.location.assign(link.href);
            });
        });
    });
</script>
@endsection
