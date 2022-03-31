<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScreenshotUpdateRequest extends FormRequest
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
                "position_x" => ["integer"],
                "position_y" => ["integer"],
                "web_position_x" => ["integer"],
                "web_position_y" => ["integer"],
                "selector" => ["string"],
                "base64" => ["string"]
            ];
        }
    }
}
