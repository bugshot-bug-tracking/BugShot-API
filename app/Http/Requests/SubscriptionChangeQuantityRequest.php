<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionChangeQuantityRequest extends FormRequest
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
			'price_api_id' => ['required', 'string', 'max:30'],
			'type' => [
                'string',
                'required',
                Rule::in([
                    'increment',
                    'decrement'
                ]),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }
}
