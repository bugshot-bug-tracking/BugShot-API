<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionAssignRequest extends FormRequest
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
			"subscription_item_id" => ["required", "string", "max:30"],
            "restricted_subscription_usage" => ["boolean"],
            "user_id" => ["required", "integer", "exists:users,id"]
		];
    }
}
