<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
			"user_id" => ["required", "integer", "exists:App\Models\User,id"],
			"content" => ["required", "max:255"],
		];
	}
}
