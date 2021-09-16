<?php

namespace App\Http\Middleware;

use App\Services\LoadService;
use Closure;
use Illuminate\Http\Request;

class AllowService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $serviceKey = $request->service_key;
        if (LoadService::getServiceByKey($serviceKey)) {
            return $next($request);
        }
        return redirect(404);
    }
}
