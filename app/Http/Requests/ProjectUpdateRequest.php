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
}
