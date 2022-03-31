<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivity
{
    /**
     * Updates an authenticated users last_active_at attribute.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is logged in, if so, update the last_active_at column on the corresponding client relation
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $clientId = $request->header('clientId');
  
            // Check if the intermediate entry already exists and create/update it
            if($user->clients()->where('client_id', $clientId)->exists()) {
                $user->clients()->updateExistingPivot($clientId, ['last_active_at' => date('Y-m-d H:i:s')]);
            } else {
                $user->clients()->attach($clientId, ['last_active_at' => date('Y-m-d H:i:s')]);  
            }
        }

        return $next($request);
    }
}
