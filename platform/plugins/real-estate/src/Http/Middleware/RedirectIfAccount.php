<?php

namespace Botble\RealEstate\Http\Middleware;

use Botble\Theme\Facades\AdminBar;
use Closure;
use Illuminate\Support\Facades\Auth;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Facades\RealEstateHelper;

class RedirectIfAccount
{
    public function handle($request, Closure $next, $guard = 'account')
    {
        if (! RealEstateHelper::isLoginEnabled()) {
            abort(404);
        }

        if (Auth::guard($guard)->check()) {
            return redirect(route('public.account.dashboard'));
        }

        AdminBar::setIsDisplay(false);
        OptimizerHelper::disable();

        return $next($request);
    }
}
