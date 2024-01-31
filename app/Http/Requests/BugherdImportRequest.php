<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BugherdImportRequest extends FormRequest
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
			"bugherd_api_token" => ["required", "string", "min:1", "max:32"],
			"projects" => ["required", "array"],
			// "projects.*" => ["required", "string", "min:1", "max:32"],
		];
    }
}
