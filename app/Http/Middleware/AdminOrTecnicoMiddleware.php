<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrTecnicoMiddleware
{
    /**
     * Only allow authenticated users with role 'admin' or 'tecnico'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdminOrTecnico()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acceso denegado.'], 403);
            }

            abort(403, 'No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
