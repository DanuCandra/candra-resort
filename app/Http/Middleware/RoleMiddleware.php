<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless($request->user(), 401);

        $allowed = collect($roles)->contains(
            fn (string $role): bool => $request->user()->hasRole($role)
        );

        abort_unless($allowed, 403);

        return $next($request);
    }
}
