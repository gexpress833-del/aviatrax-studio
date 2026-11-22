<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aviatrax Studio')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        /* No artificial global loading indicator: real navigation is handled by the browser. */

        #global-progress {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 100000;
            height: 3px;
            transform: scaleX(0);
            transform-origin: left;
            opacity: 0;
            pointer-events: none;
            background: linear-gradient(90deg, #38bdf8, #818cf8, #fb923c);
            box-shadow: 0 0 14px rgba(129, 140, 248, 0.7);
            transition: transform 180ms ease, opacity 120ms ease;
        }

        #global-progress.is-active {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <div id="global-progress" role="progressbar" aria-label="Chargement" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
    @auth
    <nav class="bg-slate-950/95 border-b border-orange-500/70 shadow-lg shadow-slate-950/40 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex min-h-16 flex-wrap items-center justify-between gap-y-3 py-3">
                <div class="flex w-full items-center justify-between lg:w-auto lg:justify-start lg:gap-6">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Aviatrax Studio, accueil">
                            <img src="{{ asset('logo_aviatrax-studio.jfif') }}" alt="Aviatrax Studio" class="h-10 w-10 rounded-xl bg-slate-100 object-contain p-1">
                        </a>
                    </div>
                    <div class="hidden lg:flex lg:items-center lg:space-x-6">
                        <a href="{{ route('dashboard') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Dashboard</a>
                        <a href="{{ route('projets.index') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Projets</a>
                        @if($user = auth()->user())
                            @if($user->isArtiste())
                                <a href="{{ route('subscriptions.index') }}" class="text-blue-300 hover:text-blue-200 transition-colors">Abonnement</a>
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

                    <a id="notification-toggle" href="{{ route('notifications.index') }}" class="relative flex items-center justify-center w-10 h-10 rounded-full bg-slate-800 border border-slate-700 text-slate-100 hover:border-blue-400 hover:text-blue-300 transition" aria-label="Voir toutes les notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                        </svg>
                        <span id="notification-count" class="{{ $unreadNotificationsCount > 0 ? 'flex' : 'hidden' }} absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 items-center justify-center text-[10px] font-bold rounded-full bg-red-500 text-white border border-slate-950">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                    </a>

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
                        <a href="{{ route('subscriptions.index') }}" class="rounded-lg px-3 py-2 text-blue-200 hover:bg-slate-800">Abonnement</a>
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
            function setActionButtonLoading(button, isLoading) {
                if (!(button instanceof HTMLButtonElement)) return;

                if (!isLoading) {
                    button.disabled = false;
                    button.classList.remove('action-loading');
                    button.removeAttribute('aria-busy');
                    button.querySelector('.action-spinner')?.remove();
                    delete button.dataset.loading;
                    return;
                }

                if (button.dataset.loading === 'true') return;

                button.dataset.loading = 'true';
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
                button.classList.add('action-loading');

                const spinner = document.createElement('span');
                spinner.className = 'action-spinner';
                spinner.setAttribute('aria-hidden', 'true');
                button.prepend(spinner);
            }

            document.addEventListener('submit', function (event) {
                if (event.defaultPrevented) return;

                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;

                const submitButton = event.submitter instanceof HTMLButtonElement
                    ? event.submitter
                    : form.querySelector('button[type="submit"]:not([disabled]), button:not([type]):not([disabled])');

                setActionButtonLoading(submitButton, true);

                window.setTimeout(function () {
                    setActionButtonLoading(submitButton, false);
                }, 25000);
            });

            window.addEventListener('pageshow', function () {
                document.querySelectorAll('button.action-loading').forEach(function (button) {
                    setActionButtonLoading(button, false);
                });
            });

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
        });
    </script>
</body>
</html>