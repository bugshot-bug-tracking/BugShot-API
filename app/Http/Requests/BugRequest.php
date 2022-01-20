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
			"designation" => ["required", "string", "min:5", "max:255"],
			"description" => ["min:5", "string", "nullable"],
			"url" => ["string", "max:65535", "nullable"],
			"priority_id" => ["required", "integer", "exists:App\Models\Priority,id"],
			"operating_system" => ["max:255", "string", "nullable"],
			"browser" => ["max:255", "string", "nullable"],
			"selector" => ["max:65535", "string", "nullable"],
			"resolution" => ["max:255", "string", "nullable"],
			"deadline" => ["date"],
			"order_number" => ["integer", "min:0"]
		];
	}
}
