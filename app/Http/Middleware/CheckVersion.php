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
        $client_version = Version::where('client_id', $request->header('clientId'))->first();
   
        if($client_version != NULL) {
            if($request->header('version') == $client_version->designation && $client_version->supported == true) {
                return $next($request);
            } 
        }

        $response = [
            'success' => false,
            'message' => 'wrong_version',
        ];

        return response()->json($response, 503);
    }
}
