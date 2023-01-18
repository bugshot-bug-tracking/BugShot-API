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
        $client = $request->header('clientId');
        $client_version = Version::where([
            ['client_id', '=', $client],
            ['designation', '=', $request->header('version')],
            ['supported', '=', true]
        ])->first();

        if($client_version != NULL) {
            $request->attributes->add(['client_id' => $client]);
            $request->attributes->add(['session_id' => $request->header('session-id')]);
            return $next($request);
        }

        $response = [
            'success' => false,
            'message' => 'wrong_version',
        ];

        return response()->json($response, 503);
    }
}
