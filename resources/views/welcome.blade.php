<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Aviatrax Studio</title>
        <meta name="description" content="Aviatrax Studio accompagne les artistes et studios dans la gestion de projets musicaux, la production et la collaboration." />
        @vite('resources/css/app.css')
    </head>
    <body class="bg-slate-950 text-slate-100 antialiased">
        <div class="min-h-screen">
            <header class="absolute inset-x-0 top-0 z-20">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 font-black text-lg text-white shadow-glow">A</div>
                        <span class="text-xl font-semibold tracking-tight text-white">Aviatrax Studio</span>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="rounded-full border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:border-orange-400 hover:bg-orange-500/10">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="rounded-full border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:border-orange-400 hover:bg-orange-500/10">Connexion</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-400">Inscription</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </nav>
            </header>

            <main>
                <section class="relative isolate overflow-hidden">
                    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(249,115,22,0.18),_transparent_45%)]"></div>
                    <div class="mx-auto grid max-w-7xl gap-12 px-6 pb-20 pt-32 lg:grid-cols-2 lg:items-center lg:px-8 lg:pt-40">
                        <div>
                            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-orange-500/40 bg-orange-500/10 px-3 py-1 text-sm font-medium text-orange-200">
                                <span class="h-2 w-2 rounded-full bg-orange-400"></span>
                                Studio de production & artistes
                            </div>

                            <h1 class="max-w-xl text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">
                                Donnez vie à vos projets musicaux avec <span class="text-orange-400">Aviatrax Studio</span>
                            </h1>

                            <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">
                                Plateforme pensée pour les artistes, studios et équipes de production : suivi des projets, gestion des étapes, collaboration fluide et pilotage plus intelligent.
                            </p>

                            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-orange-500 px-6 py-3.5 text-base font-semibold text-white shadow-glow transition hover:bg-orange-400">
                                        Créer mon compte
                                    </a>
                                @endif
                                <a href="#services" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 py-3.5 text-base font-semibold text-white transition hover:border-orange-400 hover:bg-orange-500/10">
                                    Découvrir les services
                                </a>
                            </div>

                            <dl class="mt-10 grid max-w-xl grid-cols-3 gap-5">
                                <div>
                                    <dt class="text-3xl font-black text-white">120+</dt>
                                    <dd class="mt-1 text-sm text-slate-400">projets suivis</dd>
                                </div>
                                <div>
                                    <dt class="text-3xl font-black text-white">4x</dt>
                                    <dd class="mt-1 text-sm text-slate-400">plus de fluidité</dd>
                                </div>
                                <div>
                                    <dt class="text-3xl font-black text-white">24/7</dt>
                                    <dd class="mt-1 text-sm text-slate-400">suivi en ligne</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="relative">
                            <div class="absolute -inset-6 rounded-[2rem] bg-orange-500/15 blur-3xl"></div>
                            <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 shadow-2xl backdrop-blur">
                                <div class="mb-6 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-slate-400">Pipeline de production</p>
                                        <h2 class="text-xl font-bold text-white">Tableau de bord</h2>
                                    </div>
                                    <span class="rounded-full bg-emerald-500/15 px-2.5 py-1 text-xs font-semibold text-emerald-300">Actif</span>
                                </div>

                                <div class="space-y-4">
                                    <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                                        <div class="mb-3 flex items-center justify-between text-sm text-slate-300">
                                            <span>Projet en cours</span>
                                            <span class="font-medium text-orange-300">78%</span>
                                        </div>
                                        <div class="h-2.5 rounded-full bg-slate-800">
                                            <div class="h-2.5 w-[78%] rounded-full bg-gradient-to-r from-orange-500 to-amber-400"></div>
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                                            <p class="text-sm text-slate-400">Artistes</p>
                                            <p class="mt-2 text-3xl font-black text-white">32</p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                                            <p class="text-sm text-slate-400">Tâches validées</p>
                                            <p class="mt-2 text-3xl font-black text-white">148</p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                                        <div class="mb-4 flex items-center justify-between">
                                            <p class="text-sm text-slate-400">Étapes</p>
                                            <p class="text-sm font-medium text-orange-300">Cette semaine</p>
                                        </div>
                                        <ul class="space-y-3 text-sm text-slate-300">
                                            <li class="flex items-center justify-between rounded-xl bg-slate-900 px-3 py-2"><span>Pré-production</span><span class="text-emerald-300">OK</span></li>
                                            <li class="flex items-center justify-between rounded-xl bg-slate-900 px-3 py-2"><span>Enregistrement</span><span class="text-amber-300">En cours</span></li>
                                            <li class="flex items-center justify-between rounded-xl bg-slate-900 px-3 py-2"><span>Mixage</span><span class="text-slate-400">À venir</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="services" class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Services</p>
                        <h2 class="mt-4 text-3xl font-black tracking-tight text-white sm:text-4xl">Tout ce qu’il faut pour réussir un projet</h2>
                    </div>

                    <div class="mt-12 grid gap-6 md:grid-cols-3">
                        <article class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-500/10 text-2xl text-orange-300">🎵</div>
                            <h3 class="text-xl font-bold text-white">Suivi de production</h3>
                            <p class="mt-3 text-slate-300">Organisez les étapes, les livrables et les validations de chaque projet en un seul endroit.</p>
                        </article>

                        <article class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-500/10 text-2xl text-orange-300">🤝</div>
                            <h3 class="text-xl font-bold text-white">Collaboration</h3>
                            <p class="mt-3 text-slate-300">Les artistes, responsables studio et équipes travaillent avec un accès clair et des flux simplifiés.</p>
                        </article>

                        <article class="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-500/10 text-2xl text-orange-300">📊</div>
                            <h3 class="text-xl font-bold text-white">Pilotage visuel</h3>
                            <p class="mt-3 text-slate-300">Suivez les avancements, repérez les blocages et optimisez le temps de production.</p>
                        </article>
                    </div>
                </section>

                <section class="bg-slate-900/70 py-20">
                    <div class="mx-auto grid max-w-7xl gap-8 px-6 lg:grid-cols-2 lg:px-8">
                        <div class="rounded-3xl border border-slate-800 bg-slate-950 p-8">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Pourquoi nous</p>
                            <h2 class="mt-4 text-3xl font-black text-white">Une plateforme conçue pour la musique et la création</h2>
                            <ul class="mt-6 space-y-4 text-slate-300">
                                <li>• Gestion centralisée des projets et des validations</li>
                                <li>• Interface claire pour les artistes et les équipes internes</li>
                                <li>• Suivi des étapes avec visibilité en temps réel</li>
                                <li>• Démarrage rapide et onboarding simple</li>
                            </ul>
                        </div>

                        <div class="rounded-3xl border border-orange-500/30 bg-gradient-to-br from-orange-500/10 to-transparent p-8">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-300">Processus</p>
                            <h2 class="mt-4 text-3xl font-black text-white">De l’idée à la livraison</h2>
                            <div class="mt-8 space-y-6">
                                <div class="flex gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-500 font-bold text-white">1</div>
                                    <div>
                                        <h3 class="font-bold text-white">Créer le projet</h3>
                                        <p class="mt-1 text-slate-300">Définissez les objectifs, les livrables et les responsabilités.</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-500 font-bold text-white">2</div>
                                    <div>
                                        <h3 class="font-bold text-white">Suivre les étapes</h3>
                                        <p class="mt-1 text-slate-300">Chaque étape est visible, validée et ajustée selon le besoin.</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-500 font-bold text-white">3</div>
                                    <div>
                                        <h3 class="font-bold text-white">Livrer avec qualité</h3>
                                        <p class="mt-1 text-slate-300">Validez la production, finalisez et boostez votre présence musicale.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                    <div class="rounded-[2rem] border border-orange-500/20 bg-orange-500/10 p-8 text-center lg:p-12">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-300">Prêt à commencer ?</p>
                        <h2 class="mt-4 text-3xl font-black text-white sm:text-4xl">Créez votre compte et lancez votre prochain projet.</h2>
                        <div class="mt-8 flex flex-col justify-center gap-4 sm:flex-row">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-orange-500 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-orange-400">S’inscrire</a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 py-3.5 text-base font-semibold text-white transition hover:border-orange-400 hover:bg-orange-500/10">Se connecter</a>
                            @endif
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-800 bg-slate-950/80">
                <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 py-8 text-sm text-slate-400 sm:flex-row lg:px-8">
                    <p>© {{ date('Y') }} Aviatrax Studio</p>
                    <div class="flex items-center gap-6">
                        <a href="{{ route('login') }}" class="hover:text-white">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hover:text-white">Inscription</a>
                        @endif
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
