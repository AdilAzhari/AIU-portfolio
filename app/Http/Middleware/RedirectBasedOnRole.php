<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role) {
            return $next($request);
        }

        // Map roles to their dashboard routes
        $dashboardRoutes = [
            RoleEnum::ADMIN->value => '/admin/dashboard',
            RoleEnum::ISSUER->value => '/issuer/dashboard',
            RoleEnum::STUDENT->value => '/student/dashboard',
            RoleEnum::VERIFIER->value => '/verifier/dashboard',
        ];

        $roleName = $user->role->name->value;

        // If user is on /dashboard, redirect to their role-specific dashboard
        if ($request->is('dashboard') && isset($dashboardRoutes[$roleName])) {
            return redirect($dashboardRoutes[$roleName]);
        }

        return $next($request);
    }
}
