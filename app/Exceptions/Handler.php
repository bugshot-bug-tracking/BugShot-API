<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * The list of the inputs that are never flashed to the session on validation exceptions.
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 */
	public function register(): void
	{
		$this->reportable(function (Throwable $e) {
			if (app()->bound('sentry')) {
				app('sentry')->captureException($e);
			}
		});
	}

	// Reporting for Sentry tool
	public function report(Throwable $exception)
	{
		if (app()->bound('sentry') && $this->shouldReport($exception)) {
			app('sentry')->captureException($exception);
		}

		parent::report($exception);
	}
}
