<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener locale del header Accept-Language
        $locale = $request->header('Accept-Language', config('app.locale'));

        // Validar que sea un locale soportado
        $supportedLocales = config('app.available_locales', ['es', 'en', 'pt']);

        // Limpiar el locale (puede venir como "es-ES" o "es")
        $locale = strtolower(substr($locale, 0, 2));

        // Si no es soportado, usar el fallback
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.fallback_locale', 'es');
        }

        // Establecer el locale de la aplicación
        app()->setLocale($locale);

        return $next($request);
    }
}
