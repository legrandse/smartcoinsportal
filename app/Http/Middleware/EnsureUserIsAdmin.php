<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On vérifie si l'utilisateur est connecté ET s'il est admin
	    if (auth()->check() && auth()->user()->is_admin) {
	        return $next($request);
	    }

	    // Sinon, on bloque (403 Forbidden) ou on redirige
	    abort(403, "Accès refusé : vous n'êtes pas administrateur.");
	    }
}
