<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScreenshotRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"bug_id" => ["required", "exists:App\Models\Bug,id"],
			"designation" => ["required", "max:255"],
			"url" => ["required"],
			"position_x" => ["integer"],
			"position_y" => ["integer"],
			"web_position_x" => ["integer"],
			"web_position_y" => ["integer"],
		];
	}
}
