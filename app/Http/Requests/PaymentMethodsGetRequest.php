<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodsGetRequest extends FormRequest
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
			'type' => [
                'string',
                'nullable',
                Rule::in([
                    'card', // Card payments are supported through many networks and card brands.
                    'customer_balance', // Uses a customer’s cash balance for the payment.
                    'eps', // EPS is an Austria-based bank redirect payment method.
                    'giropay', // giropay is a German bank redirect payment method.
                    'ideal', // iDEAL is a Netherlands-based bank redirect payment method.
                    'klarna', // Klarna is a global buy now, pay later payment method.,
                    'link', // Link allows customers to pay with their saved payment details.
                    'sepa_debit', // SEPA Direct Debit is used to debit bank accounts within the Single Euro Payments Area (SEPA) region.
                    'sofort' // Sofort is a bank redirect payment method used in Europe.
                ]), 
            ]
		];
    }
}
