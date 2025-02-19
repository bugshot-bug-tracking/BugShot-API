<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
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
                "id" => ["string", "max:36"],
                "designation" => ["required", "string", "min:1", "max:255"],
                "url" => ["string", "max:65535", "nullable"],
                "base64" => ["string", "nullable"],
                "color_hex" => ["string", "max:7", "nullable"],
                "timestamp" => ["date"]
            ];
        }
    }

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		if($this->has("color_hex")){
			$this->merge([
				// Thee color_hex can be null but that should set the value to a default one.
				'color_hex' => is_null($this->color_hex) ? "#7A2EE6" : $this->color_hex,
			]);
		}
	}
}
