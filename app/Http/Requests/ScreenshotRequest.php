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
			"bug_id" => ["required", "integer", "exists:App\Models\Bug,id"],
			"position_x" => ["required", "integer"],
			"position_y" => ["required", "integer"],
			"web_position_x" => ["integer"],
			"web_position_y" => ["integer"],
			"file" => ["required", "file", "max:5200"],
		];
	}
}
