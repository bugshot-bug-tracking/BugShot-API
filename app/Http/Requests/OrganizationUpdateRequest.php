<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationUpdateRequest extends FormRequest
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
				"groups_wording" => ["required", "string", "min:1", "max:255"],
                // "street" => ["required", "string", "min:1", "max:255"],
                // "housenumber" => ["required", "string", "min:1", "max:255"],
                // "city" => ["required", "string", "min:1", "max:255"],
                // "state" => ["required", "string", "min:1", "max:255"],
                // "zip" => ["required", "string", "min:1", "max:255"],
                // "country" => ["required", "string", "min:1", "max:255"],
                // "tax_id" => ["required", "string", "min:1", "max:255"],
                "timestamp" => ["date"]
            ];
        }
    }
}
