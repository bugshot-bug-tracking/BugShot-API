<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackStoreRequest extends FormRequest
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
            "name" => ["prohibited"],
			"designation" => ["required", "string", "min:1", "max:255"],
			"description" => ["min:1", "max:1500", "string", "nullable"],
			"url" => ["string", "max:65535", "nullable"],
			"operating_system" => ["max:255", "string", "nullable"],
			"browser" => ["max:255", "string", "nullable"],
			"selector" => ["max:65535", "string", "nullable"],
			"resolution" => ["max:255", "string", "nullable"]
		];
    }
}
