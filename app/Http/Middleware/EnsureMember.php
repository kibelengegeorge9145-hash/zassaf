<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMember
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_active || ! $request->user()->isMember()) {
            return redirect()->route('member.login')
                ->with('error', __('membership.login_required'));
        }

        return $next($request);
    }
}
