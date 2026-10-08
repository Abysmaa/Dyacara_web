<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        abort_unless(Auth::user()->isAdmin(), 403);

        return $next($request);
    }
}
