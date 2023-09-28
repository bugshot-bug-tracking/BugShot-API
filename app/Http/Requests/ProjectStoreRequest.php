<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
			"id" => ["string", "max:36"],
			"designation" => ["required", "string", "min:1", "max:255"],
			"url" => ["string", "max:65535", "nullable"],
			"access_token" => ["string", "max:65535", "nullable"],
			"base64" => ["string", "nullable"],
			"color_hex" => ["required", "string", "max:7", "nullable"],
			"timestamp" => ["date"]
		];
    }
}
