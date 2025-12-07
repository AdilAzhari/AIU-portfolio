<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, string $role)
    {
        if (! $request->user() || ! $request->user()->role || $request->user()->role->name->value !== $role) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
