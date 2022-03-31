<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusUpdateRequest extends FormRequest
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
                "designation" => ["required", "string", "min:1", "max:255"],
                "order_number" => ["required", "integer", "min:0"],
                "timestamp" => ["date"]
            ];
        }
    }
}
