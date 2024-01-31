<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessTokenUpdateRequest extends FormRequest
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
		if ($this->isMethod('patch')) {
            return [];
        } else {
			return [
				"description" => ["min:1", "max:1500", "string", "nullable"]
			];
        }
    }
}
