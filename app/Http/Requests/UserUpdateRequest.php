<?php

namespace App\Http\Requests;

use App\Rules\OldPasswordConfirmed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
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
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)->whereNull('deleted_at')],
                'old_password' => ['required', new OldPasswordConfirmed($this->user)],
                'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
                'password_confirmation' => ['exclude_unless:password,true','required', 'same:password']
            ];
        }
    }
}
