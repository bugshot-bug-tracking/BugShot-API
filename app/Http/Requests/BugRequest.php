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
			"user_id" => ["required", "exists:App\Models\User,id"],
			"project_id" => ["required", "exists:App\Models\Project,id"],
			"designation" => ["required", "max:255"],
			"description" => ["required"],
			"url" => ["required"],
			"status_id" => ["required", "exists:App\Models\Status,id"],
			"priority_id" => ["required", "exists:App\Models\Priority,id"],
		];
	}
}
