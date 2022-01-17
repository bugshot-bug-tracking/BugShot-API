<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
			'first_name' => ['required', 'alpha_dash', 'max:255'],
			'last_name' => ['required', 'alpha_dash', 'max:255'],
			'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id),],
			'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
			'password_confirmation' => ['required', 'same:password']
        ];
    }
}
