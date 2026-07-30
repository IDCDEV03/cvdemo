<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Role;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $allowedRoles = array_map(fn($role) => Role::from($role), $roles);

        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
