<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BugRequest extends FormRequest
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
			"project_id" => ["required", "string", "exists:App\Models\Project,id"],
			"designation" => ["required", "string", "min:5", "max:255"],
			"description" => ["min:5", "string"],
			"url" => ["string", "max:65535", "nullable"],
			"priority_id" => ["required", "integer", "exists:App\Models\Priority,id"],
			"operating_system" => ["max:255", "string"],
			"browser" => ["max:255", "string"],
			"selector" => ["max:65535", "string"],
			"resolution" => ["max:255", "string"],
			"deadline" => ["date"],
		];
	}
}
