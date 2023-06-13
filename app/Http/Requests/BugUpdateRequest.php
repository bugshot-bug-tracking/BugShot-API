<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BugUpdateRequest extends FormRequest
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
                "designation" => ["string", "min:1", "max:70"],
                "description" => ["min:1", "max:1500", "string", "nullable"],
                "url" => ["string", "max:65535", "nullable"],
                "priority_id" => ["integer", "exists:App\Models\Priority,id"],
                "operating_system" => ["max:255", "string", "nullable"],
                "browser" => ["max:255", "string", "nullable"],
                "selector" => ["max:65535", "string", "nullable"],
                "resolution" => ["max:255", "string", "nullable"],
                "deadline" => ["date", "nullable"],
                "order_number" => ["integer", "min:0"],
                "status_id" => ["string", "exists:App\Models\Status,id"],
				"time_estimation_type" => ["required", "string", "size:1", "in:w,d,h,m"],
            ];
        }
    }
}
