<?php

namespace App\Http\Middleware;

use App\Models\Version;
use Illuminate\Http\Request;
use Closure;

class CheckVersion
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
        $client_version = Version::where([
            ['client_id', '=', $request->header('clientId')],
            ['designation', '=', $request->header('version')],
            ['supported', '=', true]
        ])->first();

        if($client_version != NULL) {
            $request->attributes->add(['client_id' => $request->header('clientId')]);
            return $next($request);
        }

        $response = [
            'success' => false,
            'message' => 'wrong_version',
        ];

        return response()->json($response, 503);
    }
}
