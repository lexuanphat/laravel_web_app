<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTransportInit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $check = \DB::table('tokens')->exists();
        if($check === false) {
            return redirect()->route('admin.token_transport');
        }
        return $next($request);
    }
}
