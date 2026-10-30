<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Détecter la langue du navigateur
        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

        // Liste des langues supportées
        $availableLocales = Config::get('app.locales', ['en', 'fr', 'es', 'de']);

        // Définir la langue de l'application
        if (in_array($browserLocale, $availableLocales)) {
            App::setLocale($browserLocale);
        } else {
            App::setLocale(Config::get('app.fallback_locale'));
        }

        return $next($request);
    }
}
