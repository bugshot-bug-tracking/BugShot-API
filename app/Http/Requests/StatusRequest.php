<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
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
			"designation" => ["required", "max:255", "regex:/^[\pL\s\-]+$/"],
			"project_id" => ["required", "integer", "exists:App\Models\Project,id"],
		];
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function messages()
	{
		return [
			"designation.regex" => "The designation must only contain letters and spaces."
		];
	}
}
