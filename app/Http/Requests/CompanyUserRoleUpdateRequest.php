<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUserRoleUpdateRequest extends FormRequest
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
        // Check if the request method is of type PATCH or POST and validate accordingly
        if ($this->isMethod('patch')) {
            return [];
        } else {
            return [
                "role_id" => ["required", "integer", "exists:roles,id"]
            ];
        }
    }
}
