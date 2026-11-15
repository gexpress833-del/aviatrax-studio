<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureArtistManagementAccess
{
    /**
     * Vérifie qu’un utilisateur autorisé peut gérer les comptes artistes.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->canManageArtistes()) {
            abort(403, 'Accès interdit : seuls l’admin, le secrétariat et le responsable studio peuvent gérer les comptes artistes.');
        }

        return $next($request);
    }
}
