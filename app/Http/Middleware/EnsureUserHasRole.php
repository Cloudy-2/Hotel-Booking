<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response|RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login')->with([
                'feedback_type' => 'info',
                'feedback_title' => 'Sign in required',
                'status' => 'Please sign in with a management account to continue.',
            ]);
        }

        if (! in_array($request->user()->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
