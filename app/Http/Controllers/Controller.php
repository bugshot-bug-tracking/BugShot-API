<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	// Checks if request contains an uuid and sets the id accordingly
	public function setId($request) {
		$id = $request->id == NULL ? (string) Str::uuid() : $request->id;
		
		return $id;
	}
}

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="BugShot API Documentation",
 *      description="OpenApi documentation using swagger for BugShot project.",
 * 		termsOfService="https://www.google.ro/",
 *      @OA\Contact(
 *          email="r.memetea@proxima-idea.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 */
