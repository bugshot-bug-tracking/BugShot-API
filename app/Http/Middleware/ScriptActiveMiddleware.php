<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ScriptActiveMiddleware
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
		if(config("app.script_active")) {
			return $next($request);
		} else {
			return response()->json(["message" => "Scripts are disabled"], 400);
		}
    }
}
