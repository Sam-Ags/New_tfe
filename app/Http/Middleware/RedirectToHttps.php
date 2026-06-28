<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToHttps
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->isProduction() && ! $request->isSecure()) {
            return redirect()->to('https://'.$request->getHost().$request->getRequestUri(), 308);
        }

        return $next($request);
    }
}
