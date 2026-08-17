<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_active || ! $request->user()->canAdmin()) {
            return redirect()->route('admin.login')
                ->with('error', __('admin.login_required'));
        }

        return $next($request);
    }
}
