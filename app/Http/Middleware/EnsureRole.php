<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Usage: ->middleware('role:admin') or ->middleware('role:admin,teacher')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte est désactivé. Contactez l\'administrateur.',
            ]);
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Accès refusé : rôle insuffisant.');
        }

        return $next($request);
    }
}
