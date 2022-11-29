<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use App\Services\ApiTokenService;

class AuthApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) //, ApiTokenService $apiTokenService)
    {
        $apitoken_entry = (new ApiTokenService)->getModelToApiToken($request->header('api-token'));

        if($apitoken_entry != NULL) {
            $request->attributes->add(['project' => $apitoken_entry]);
            return $next($request);
        }

        $response = [
            'success' => false,
            'message' => 'unauthenticated',
        ];

        return response()->json($response, 401);
    }
}
