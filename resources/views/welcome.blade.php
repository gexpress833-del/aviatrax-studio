<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviatrax Studio — Distribution Mondiale & Production Musicale</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased selection:bg-indigo-500 selection:text-white">
    <nav class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-5 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-4">
            <div class="flex items-center justify-center gap-3 sm:justify-start">
                <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Aviatrax Studio, accueil"><img src="{{ asset('logo_aviatrax-studio.jfif') }}" alt="Aviatrax Studio" class="h-11 w-11 rounded-xl object-contain bg-slate-100 p-1"></a>
                <span class="hidden md:inline-block text-xs text-slate-400 border-l border-slate-700 pl-3">
                    Par <strong class="text-slate-200">Aviator Music</strong> &amp; <strong class="text-slate-200">Maisha Sound Studio</strong>
                </span>
            </div>
            <div class="flex items-center justify-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/20">Créer mon compte</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <header class="relative overflow-hidden pt-16 pb-24 px-6">
        <div class="max-w-5xl mx-auto text-center">
            <div class="mb-8 flex flex-wrap items-center justify-center gap-4 sm:gap-6" aria-label="Aviator Music et Maisha Sound Studio">
                <div class="flex h-24 w-32 items-center justify-center overflow-hidden rounded-xl border border-slate-700 bg-white p-2 shadow-lg shadow-black/20 sm:h-28 sm:w-40">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo Aviator Music" class="h-full w-full object-contain" width="160" height="112">
                </div>
                <span class="text-xl font-bold text-slate-500" aria-hidden="true">×</span>
                <div class="flex h-24 w-32 items-center justify-center overflow-hidden rounded-xl border border-slate-700 bg-black p-2 shadow-lg shadow-black/20 sm:h-28 sm:w-40">
                    <img src="{{ asset('logoMS.jpg') }}" alt="Logo Maisha Sound Studio" class="h-full w-full object-contain" width="160" height="112">
                </div>
            </div>

            <div class="inline-flex items-center gap-2 bg-slate-900 border border-slate-800 text-slate-300 text-xs font-medium px-4 py-1.5 rounded-full mb-8">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Un produit du label <strong class="text-white">Aviator Music</strong> en collaboration avec <strong class="text-white">Maisha Sound Studio</strong>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white mb-6 leading-tight">
                Propulsez votre musique sur <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-500">toutes les plateformes mondiales</span>.
            </h1>

            <p class="text-lg sm:text-xl text-slate-300 mb-10 max-w-3xl mx-auto leading-relaxed">
                Créez votre compte gratuitement, puis choisissez l'abonnement de distribution à <strong class="text-white">{{ number_format((float) config('aviatrax.subscription_amount', 12), 0) }} $ / {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours</strong> pour publier vos morceaux sur Spotify, Apple Music, Boomplay et TikTok. Vous pouvez aussi donner vie à vos projets grâce à notre studio de référence.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 px-8 rounded-xl shadow-xl shadow-indigo-600/25 transition duration-200 text-base">Créer mon compte</a>
                @endif
                <a href="#services" class="bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold py-4 px-8 rounded-xl transition duration-200 text-base">Découvrir nos services</a>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-6 text-xs text-slate-400">
                <span>✓ Spotify • Apple Music • Boomplay • Audiomack • Deezer</span>
                <span>✓ 100% de vos droits conservés</span>
                <span>✓ Retraits en argent réel</span>
            </div>
        </div>
    </header>

    <section class="border-y border-slate-800 bg-slate-900/50 px-5 py-16 sm:px-6 sm:py-20" aria-labelledby="platforms-title">
        <div class="mx-auto max-w-7xl">
            <div class="mx-auto mb-10 max-w-3xl text-center sm:mb-12">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-300">Distribution mondiale</p>
                <h2 id="platforms-title" class="mt-3 text-3xl font-bold leading-tight text-white sm:text-4xl">Votre musique, partout où votre public l’écoute</h2>
                <p class="mt-4 text-sm leading-6 text-slate-300 sm:text-base">Une large sélection de plateformes de streaming et de téléchargement pour donner à vos titres une portée internationale.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
                <figure class="overflow-hidden rounded-2xl border border-slate-700 bg-white shadow-xl shadow-black/20">
                    <div class="flex aspect-[4/3] items-center justify-center p-3 sm:p-5 lg:aspect-[5/4]">
                        <img src="{{ asset('distribuions musical.png') }}" alt="Catalogue de plateformes de distribution musicale, dont Spotify, iTunes, YouTube Music et Beatport" class="h-full w-full object-contain" width="969" height="969" loading="lazy" decoding="async">
                    </div>
                    <figcaption class="border-t border-slate-200 bg-white px-4 py-3 text-center text-sm font-medium text-slate-700 sm:px-6">Un réseau étendu de services musicaux dans le monde</figcaption>
                </figure>

                <figure class="overflow-hidden rounded-2xl border border-slate-700 bg-black shadow-xl shadow-black/20">
                    <div class="flex aspect-[4/3] items-center justify-center p-3 sm:p-5 lg:aspect-[5/4]">
                        <img src="{{ asset('les plateforme de distributions.jpg') }}" alt="Plateformes musicales populaires : iTunes, Apple Music, Spotify, Deezer, YouTube Music, TikTok et SoundCloud" class="h-full w-full object-contain" width="1024" height="1024" loading="lazy" decoding="async">
                    </div>
                    <figcaption class="border-t border-slate-700 bg-slate-950 px-4 py-3 text-center text-sm font-medium text-slate-200 sm:px-6">Les plateformes que vos auditeurs connaissent déjà</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section id="services" class="py-20 px-6 bg-slate-900/50 border-y border-slate-800/80">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Une offre claire, sans mauvaise surprise</h2>
                <p class="text-slate-400">Chez <strong class="text-white">Aviatrax Studio</strong>, nous séparons clairement le coût de fabrication de votre musique en studio et le coût de sa diffusion mondiale.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 items-stretch">
                <div class="bg-slate-950 border-2 border-indigo-500/60 rounded-3xl p-8 flex flex-col justify-between relative shadow-2xl">
                    <div class="absolute -top-3.5 right-8 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">Abonnement Plateforme</div>
                    <div>
                        <span class="text-indigo-400 font-semibold text-sm uppercase tracking-wider">Service 1 • Publication &amp; Royalties</span>
                        <h3 class="text-2xl font-bold text-white mt-2 mb-3">Pass Distribution Mondiale</h3>
                        <p class="text-slate-400 text-sm mb-6">Destiné à tout artiste souhaitant publier ses morceaux déjà prêts sur toutes les plateformes de streaming et encaisser ses revenus.</p>

                        <div class="mb-6 pb-6 border-b border-slate-800">
                            <span class="text-4xl font-black text-white">{{ number_format((float) config('aviatrax.subscription_amount', 12), 0) }} $</span>
                            <span class="text-slate-400 text-sm"> / {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours</span>
                        </div>

                        <ul class="space-y-3 text-sm text-slate-300 mb-8">
                            <li class="flex items-start gap-2.5"><span class="text-emerald-400 font-bold">✓</span> Publication sur Spotify, Apple Music, Boomplay, Deezer, TikTok...</li>
                            <li class="flex items-start gap-2.5"><span class="text-emerald-400 font-bold">✓</span> Fourniture des codes officiels ISRC &amp; UPC</li>
                            <li class="flex items-start gap-2.5"><span class="text-emerald-400 font-bold">✓</span> Smart Link (lien unique) de promotion mondiale</li>
                            <li class="flex items-start gap-2.5"><span class="text-emerald-400 font-bold">✓</span> Tableau de bord &amp; portefeuille de points convertibles en cash</li>
                        </ul>
                    </div>

                    <div>
                        <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-3.5 mb-5 text-xs text-amber-200">
                            <strong>⚠️ À noter :</strong> Ce tarif concerne <u>uniquement</u> la mise en ligne et le suivi sur l'application. Il n'inclut pas les frais d'enregistrement ou de mixage au studio.
                        </div>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-6 rounded-xl transition">Créer un compte pour souscrire</a>
                        @endif
                    </div>
                </div>

                <div class="bg-slate-950 border border-slate-800 rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <span class="text-pink-400 font-semibold text-sm uppercase tracking-wider">Service 2 • Maisha Sound Studio</span>
                        <h3 class="text-2xl font-bold text-white mt-2 mb-3">Production Musicale en Studio</h3>
                        <p class="text-slate-400 text-sm mb-6">Besoin d'enregistrer votre morceau avant de le publier ? Notre studio de référence <strong class="text-slate-200">Maisha Sound Studio</strong> réalise votre projet de A à Z.</p>

                        <div class="mb-6 pb-6 border-b border-slate-800">
                            <span class="text-2xl font-bold text-white">Tarif Studio Distinct</span>
                            <span class="block text-slate-400 text-xs mt-1">Facturé séparément selon la nature du projet (Single, EP, Album)</span>
                        </div>

                        <ul class="space-y-3 text-sm text-slate-300 mb-8">
                            <li class="flex items-start gap-2.5"><span class="text-pink-400 font-bold">✓</span> Prise de voix et enregistrement professionnel</li>
                            <li class="flex items-start gap-2.5"><span class="text-pink-400 font-bold">✓</span> Mixage et Mastering haute définition</li>
                            <li class="flex items-start gap-2.5"><span class="text-pink-400 font-bold">✓</span> Suivi en temps réel des étapes sur votre espace Aviatrax</li>
                            <li class="flex items-start gap-2.5"><span class="text-pink-400 font-bold">✓</span> Écoute sécurisée et validation de vos maquettes en ligne</li>
                        </ul>
                    </div>

                    <div>
                        <div class="bg-slate-900 border border-slate-800 rounded-xl p-3.5 mb-5 text-xs text-slate-400">
                            <strong>ℹ️ Fonctionnement :</strong> Une fois votre son finalisé et réglé auprès de <em>Maisha Sound Studio</em>, vous activez votre Pass Distribution à {{ number_format((float) config('aviatrax.subscription_amount', 12), 0) }} $ pour {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours.
                        </div>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 px-6 rounded-xl border border-slate-700 transition">Créer un compte artiste</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-6 max-w-5xl mx-auto">
        <div class="bg-gradient-to-r from-indigo-950/80 to-slate-900 border border-indigo-500/30 rounded-3xl p-8 sm:p-12 text-center shadow-xl">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">Transformez votre audience en revenus réels</h2>
            <p class="text-slate-300 max-w-2xl mx-auto mb-8 leading-relaxed">Chaque écoute sur Spotify, Apple Music ou Boomplay génère des royalties. Sur Aviatrax Studio, vos gains apparaissent sous forme de <strong>points transparents</strong> dans votre tableau de bord. Visez jusqu'à <strong>50 000 $</strong> de revenus selon votre audience, convertibles et retirables en cash.</p>
            <div class="grid sm:grid-cols-3 gap-6 text-left max-w-3xl mx-auto">
                <div class="bg-slate-950/70 p-5 rounded-2xl border border-slate-800">
                    <div class="text-indigo-400 font-bold text-lg mb-1">1. Diffusion</div>
                    <p class="text-xs text-slate-400 leading-relaxed">Vos fans écoutent votre musique partout dans le monde via notre agrégateur.</p>
                </div>
                <div class="bg-slate-950/70 p-5 rounded-2xl border border-slate-800">
                    <div class="text-purple-400 font-bold text-lg mb-1">2. Accumulation</div>
                    <p class="text-xs text-slate-400 leading-relaxed">Vos royalties sont créditées en points directement sur votre tableau de bord.</p>
                </div>
                <div class="bg-slate-950/70 p-5 rounded-2xl border border-slate-800">
                    <div class="text-emerald-400 font-bold text-lg mb-1">3. Retrait Cash</div>
                    <p class="text-xs text-slate-400 leading-relaxed">Convertissez vos points et encaissez via Mobile Money ou au studio.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 px-6 max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-white mb-12">Questions Fréquentes</h2>
        <div class="space-y-4">
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-lg mb-2">Quelle est la différence entre le prix de production au studio et l'abonnement de {{ number_format((float) config('aviatrax.subscription_amount', 12), 0) }} $ ?</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Ce sont deux choses totalement séparées : 1) <strong>La production au studio (Maisha Sound Studio)</strong> correspond aux frais de fabrication de votre chanson (enregistrement, mixage, mastering) selon les tarifs du studio. 2) <strong>L'abonnement de {{ number_format((float) config('aviatrax.subscription_amount', 12), 0) }} $ pour {{ (int) config('aviatrax.subscription_duration_days', 30) }} jours</strong> couvre uniquement votre accès à la plateforme Aviatrax Studio pour publier vos morceaux sur toutes les plateformes mondiales et récupérer vos revenus.</p>
            </div>
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-lg mb-2">Qui se cache derrière Aviatrax Studio ?</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Aviatrax Studio est un produit officiel développé par le label <strong>Aviator Music</strong> en collaboration étroite avec <strong>Maisha Sound Studio</strong>, garantissant une expertise complète de la création sonore à la distribution internationale.</p>
            </div>
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6">
                <h3 class="font-bold text-white text-lg mb-2">Comment je récupère mes revenus en argent réel ?</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Dès que vos streams génèrent des royalties, votre solde de points augmente dans votre espace artiste. Une fois le seuil atteint, vous demandez votre paiement via Mobile Money (M-Pesa, Airtel, Orange) ou directement auprès de l'administration.</p>
            </div>
        </div>
    </section>

    <footer class="border-t border-slate-800 py-12 px-6 text-center text-slate-400 text-sm">
        <div class="max-w-5xl mx-auto">
            <p class="font-bold text-white text-base mb-2">AVIATRAX STUDIO</p>
            <p class="mb-4">Un produit du label <span class="text-slate-200 font-medium">Aviator Music</span> en collaboration avec <span class="text-slate-200 font-medium">Maisha Sound Studio</span>.</p>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Aviator Music. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
