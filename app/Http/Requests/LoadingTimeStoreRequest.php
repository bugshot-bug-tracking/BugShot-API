<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoadingTimeStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
			"url" => ["required", "string", "min:1"],
			"loading_duration_raw" => ["required", "min:1", "integer"], // in ms / Loading duration site only
			"loading_duration_fetched" => ["min:1", "integer", "nullable"], // in ms / Loading duration with fetched content
		];
    }
}
