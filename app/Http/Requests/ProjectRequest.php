<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
			"url" => ["required", "string"],
			"company_id" => ["required", "string", "exists:App\Models\Company,id"],
			"base64" => ["string", "nullable"],
			"color_hex" => ["string", "max:7", "nullable"]
		];
	}
}
