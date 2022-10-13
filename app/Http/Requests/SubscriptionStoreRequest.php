<?php

namespace App\Http\Requests;

use App\Models\BillingAddress;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionStoreRequest extends FormRequest
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
            'subscription_name' => ['required', 'string', 'max:36'],
			'products.*.price_api_id' => ['required', 'string', 'max:30'],
			'products.*.quantity' => ['required', 'integer', 'min:1', 'max:99999'],
			'payment_method_id' => ['required', 'string', 'max:30']
		];
    }
}
