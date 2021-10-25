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
			"url" => ["required", "url"],
			"company_id" => ["required", "integer", "exists:App\Models\Company,id"],
			"image_id" => ["integer", "exists:App\Models\Image,id"]
		];
	}
}
