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
			"ids" => ["required", "array"],
			"ids.*" => ["required", "string", "min:1", "max:32"],
		];
    }
}
