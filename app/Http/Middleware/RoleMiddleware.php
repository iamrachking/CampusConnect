<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: route middleware 'role:Administrateur,Enseignant'
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            abort(403, 'Accès interdit');
        }

        $userRole = $user->role->name;

        if (!in_array($userRole, $roles, true)) {
            abort(403, 'Accès interdit');
        }

        return $next($request);
    }
}