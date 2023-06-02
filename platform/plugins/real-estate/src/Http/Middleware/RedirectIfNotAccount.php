<?php

namespace Botble\RealEstate\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Botble\RealEstate\Facades\RealEstateHelper;

class RedirectIfNotAccount
{
    public function handle($request, Closure $next, $guard = 'account')
    {
        if (! RealEstateHelper::isLoginEnabled()) {
            abort(404);
        }

        if (! Auth::guard($guard)->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(route('public.account.login'));
        }

        return $next($request);
    }
}
