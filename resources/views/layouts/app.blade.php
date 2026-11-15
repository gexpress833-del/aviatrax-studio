<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aviatrax Studio')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#3b82f6',
                        'brand-orange': '#f59e0b',
                        'brand-slate': '#0f172a',
                        'panel': '#111827',
                        'panel-soft': '#1f2937',
                        'etape-enregistrement': '#60a5fa',
                        'etape-mixage': '#f472b6',
                        'etape-mastering': '#fbbf24',
                        'etape-distribution-cours': '#fb923c',
                        'etape-distribution-terminee': '#4ade80',
                    }
                }
            }
        }
    </script>
    <style>
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            background: linear-gradient(180deg, #020817 0%, #0f172a 100%);
        }

        img, video, canvas, iframe {
            max-width: 100%;
        }

        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        main :where(input, select, textarea, button) {
            max-width: 100%;
        }

        main :where(h1, h2, h3, p, a, span, td, th) {
            overflow-wrap: break-word;
            word-break: normal;
        }

        main :where(pre, table) {
            max-width: 100%;
        }

        @media (max-width: 360px) {
            main {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    @auth
    <nav class="bg-slate-950/95 border-b border-orange-500/70 shadow-lg shadow-slate-950/40 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex min-h-16 flex-wrap items-center justify-between gap-y-3 py-3">
                <div class="flex w-full items-center justify-between lg:w-auto lg:justify-start lg:gap-6">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-lg font-bold text-blue-400 tracking-wide sm:text-xl">Aviatrax Studio</h1>
                    </div>
                    <div class="hidden lg:flex lg:items-center lg:space-x-6">
                        <a href="{{ route('dashboard') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Dashboard</a>
                        <a href="{{ route('projets.index') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Projets</a>
                        @if($user = auth()->user())
                            @if($user->isArtiste())
                                <a href="{{ route('wallet') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Portefeuille</a>
                            @endif
                            @if($user->isAdmin())
                                <a href="{{ route('admin.currency.edit') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Taux de change</a>
                            @endif
                        @endif
                        <a href="{{ route('profile.show') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Profil</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Administration</a>
                        @endif
                    </div>
                    <button id="mobile-menu-toggle" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-700 bg-slate-800 text-slate-100 lg:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="Ouvrir le menu">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <div class="flex w-full items-center justify-between gap-2 lg:w-auto lg:justify-end lg:gap-3">
                    @php
                        $user = auth()->user();
                        $latestNotifications = $user->notifications()->latest()->limit(8)->get();
                        $unreadNotificationsCount = $user->unreadNotifications()->count();
                    @endphp

                    <div id="notification-center" class="relative">
                        <button id="notification-toggle" type="button" aria-expanded="false" aria-controls="notification-panel" class="relative flex items-center justify-center w-10 h-10 rounded-full bg-slate-800 border border-slate-700 text-slate-100 hover:border-blue-400 hover:text-blue-300 transition" aria-label="Notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                            </svg>
                            <span id="notification-count" class="{{ $unreadNotificationsCount > 0 ? 'flex' : 'hidden' }} absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 items-center justify-center text-[10px] font-bold rounded-full bg-red-500 text-white border border-slate-950">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                        </button>

                        <div id="notification-panel" class="absolute right-0 top-12 hidden w-[min(90vw,360px)] bg-slate-900 border border-slate-700 rounded-xl shadow-2xl shadow-slate-950/60 z-50" role="region" aria-label="Notifications récentes">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-700">
                                <span class="text-sm font-semibold text-slate-100">Notifications</span>
                                <form method="POST" action="{{ route('notifications.read-all') }}" class="{{ $unreadNotificationsCount > 0 ? '' : 'hidden' }}">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-blue-300 hover:text-blue-200">Tout marquer comme lu</button>
                                </form>
                            </div>

                            <div id="notification-list" class="max-h-[330px] overflow-y-auto">
                                @forelse($latestNotifications as $notification)
                                    @php
                                        $data = $notification->data ?? [];
                                        $title = $data['titre'] ?? ($data['artiste_name'] ?? 'Nouvelle notification');
                                        $message = $data['etape_label'] ?? ($data['artiste_email'] ?? 'Vous avez une mise à jour importante.');
                                        $targetUrl = isset($data['projet_id']) ? route('projets.show', $data['projet_id']) : route('dashboard');
                                    @endphp

                                    <div class="border-b border-slate-800 {{ $notification->read_at ? 'bg-slate-900/50' : 'bg-blue-500/5' }}">
                                        <a href="{{ $targetUrl }}" data-read-url="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-slate-800/80 transition">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-medium text-slate-100">{{ $title }}</p>
                                                    <p class="mt-1 text-xs text-slate-300">{{ $message }}</p>
                                                </div>
                                                @if(is_null($notification->read_at))
                                                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                                                @endif
                                            </div>
                                            <div class="mt-2 text-[10px] uppercase tracking-wide text-slate-400">{{ $notification->created_at->diffForHumans() }}</div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="px-4 py-5 text-sm text-slate-400 text-center">
                                        Aucune notification pour le moment.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('profile.show') }}" class="flex items-center group">
                        @if($user->photo_path)
                            <span class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center border border-blue-400 shadow overflow-hidden">
                                <img src="{{ $user->photo_url }}" alt="Photo de profil" class="w-full h-full object-cover object-center" loading="lazy">
                            </span>
                        @else
                            <span class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center border border-blue-400 shadow">
                                <span class="text-lg font-semibold text-blue-100">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </span>
                        @endif
                        <span class="hidden sm:block ml-3 text-sm text-slate-200 group-hover:text-white">{{ $user->name }}</span>
                    </a>
                    <span class="hidden px-2.5 py-1 text-[11px] font-semibold bg-orange-500 text-slate-950 rounded-full uppercase tracking-wide md:inline-flex">{{ $user->role->name ?? 'Aucun rôle' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="sm:ml-1">
                        @csrf
                        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-orange-300 hover:text-orange-200 border border-slate-700 px-2.5 py-2 rounded-lg text-xs transition sm:px-3 sm:text-sm">Déconnexion</button>
                    </form>
                </div>
            </div>
            <div id="mobile-menu" class="hidden border-t border-slate-800 py-3 lg:hidden">
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Dashboard</a>
                    <a href="{{ route('projets.index') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Projets</a>
                    @if($user->isArtiste())
                        <a href="{{ route('wallet') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Portefeuille</a>
                    @endif
                    @if($user->canManageArtistes())
                        <a href="{{ route('secretariat.dashboard') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Gestion des artistes</a>
                        <a href="{{ route('secretariat.artistes.create') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Créer un artiste</a>
                    @endif
                    @if($user->isResponsableStudio())
                        <a href="{{ route('dashboard.responsable.index') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Suivi studio</a>
                        <a href="{{ route('dashboard.responsable.projets.create') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Créer un projet</a>
                    @endif
                    @if($user->isAdmin())
                        <a href="{{ route('admin.users') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Utilisateurs</a>
                        <a href="{{ route('admin.currency.edit') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Taux de change</a>
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Administration</a>
                    @endif
                    <a href="{{ route('profile.show') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Profil</a>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 min-h-screen">
        @if(session('success'))
            <div class="flash-message mb-5 border border-green-500/30 bg-green-500/10 text-green-200 px-4 py-3 rounded-lg shadow-lg shadow-green-950/30 transition-all duration-500">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash-message mb-5 border border-red-500/30 bg-red-500/10 text-red-200 px-4 py-3 rounded-lg shadow-lg shadow-red-950/20 transition-all duration-500">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="flash-message mb-5 border border-red-500/30 bg-red-500/10 text-red-200 px-4 py-3 rounded-lg shadow-lg shadow-red-950/20 transition-all duration-500">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenuToggle?.addEventListener('click', function () {
                const isOpen = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
                mobileMenuToggle.setAttribute('aria-expanded', String(!isOpen));
                mobileMenuToggle.setAttribute('aria-label', isOpen ? 'Ouvrir le menu' : 'Fermer le menu');
                mobileMenu.classList.toggle('hidden', isOpen);
            });

            const messages = document.querySelectorAll('.flash-message');

            messages.forEach(function (message) {
                setTimeout(function () {
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-8px)';
                    message.style.maxHeight = '0';
                    message.style.paddingTop = '0';
                    message.style.paddingBottom = '0';
                    message.style.marginBottom = '0';
                    message.style.overflow = 'hidden';

                    setTimeout(function () {
                        message.remove();
                    }, 500);
                }, 4000);
            });

            const center = document.getElementById('notification-center');
            if (!center) return;

            const toggle = document.getElementById('notification-toggle');
            const panel = document.getElementById('notification-panel');
            const list = document.getElementById('notification-list');
            const count = document.getElementById('notification-count');
            const readAllForm = center.querySelector('form[action$="/notifications/read-all"]');
            const feedUrl = @json(route('notifications.feed'));
            const csrfToken = @json(csrf_token());
            let previousUnreadCount = Number(count.textContent) || 0;

            toggle.addEventListener('click', function () {
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!isOpen));
                panel.classList.toggle('hidden', isOpen);
            });

            document.addEventListener('click', function (event) {
                if (!center.contains(event.target)) {
                    panel.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            function escapeHtml(value) {
                const element = document.createElement('span');
                element.textContent = value ?? '';
                return element.innerHTML;
            }

            async function refreshNotifications() {
                if (document.hidden) return;

                try {
                    const response = await fetch(feedUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!response.ok) return;

                    const feed = await response.json();
                    const unreadCount = Number(feed.unread_count) || 0;
                    count.textContent = unreadCount > 9 ? '9+' : String(unreadCount);
                    count.classList.toggle('hidden', unreadCount === 0);
                    count.classList.toggle('flex', unreadCount > 0);
                    readAllForm?.classList.toggle('hidden', unreadCount === 0);

                    if (unreadCount > previousUnreadCount) {
                        toggle.setAttribute('aria-label', unreadCount + ' notifications non lues');
                    } else if (unreadCount === 0) {
                        toggle.setAttribute('aria-label', 'Notifications');
                    }
                    previousUnreadCount = unreadCount;

                    if (document.activeElement?.closest('#notification-list')) return;
                    list.innerHTML = feed.notifications.length
                        ? feed.notifications.map(function (notification) {
                            const unreadStyle = notification.read ? 'bg-slate-900/50' : 'bg-blue-500/5';
                            const unreadDot = notification.read ? '' : '<span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-400 flex-shrink-0"></span>';
                            return '<div class="border-b border-slate-800 ' + unreadStyle + '">' +
                                '<a href="' + escapeHtml(notification.url) + '" data-read-url="/notifications/' + encodeURIComponent(notification.id) + '/read" class="block px-4 py-3 hover:bg-slate-800/80 transition">' +
                                    '<div class="flex items-start justify-between gap-3"><div>' +
                                        '<p class="text-sm font-medium text-slate-100">' + escapeHtml(notification.title) + '</p>' +
                                        '<p class="mt-1 text-xs text-slate-300">' + escapeHtml(notification.message) + '</p>' +
                                    '</div>' + unreadDot + '</div>' +
                                    '<div class="mt-2 text-[10px] uppercase tracking-wide text-slate-400">' + escapeHtml(notification.created_at) + '</div>' +
                                '</a></div>';
                        }).join('')
                        : '<div class="px-4 py-5 text-sm text-slate-400 text-center">Aucune notification pour le moment.</div>';
                } catch (error) {
                    console.error('Impossible d’actualiser les notifications.', error);
                }
            }

            refreshNotifications();
            window.setInterval(refreshNotifications, 5000);
            document.addEventListener('visibilitychange', refreshNotifications);

            list.addEventListener('click', async function (event) {
                const link = event.target.closest('a[data-read-url]');
                if (!link) return;

                event.preventDefault();
                try {
                    await fetch(link.dataset.readUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                } finally {
                    window.location.assign(link.href);
                }
            });

            readAllForm?.addEventListener('submit', async function (event) {
                event.preventDefault();
                try {
                    const response = await fetch(readAllForm.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (response.ok || response.redirected) await refreshNotifications();
                } catch (error) {
                    readAllForm.submit();
                }
            });
        });
    </script>
</body>
</html> 