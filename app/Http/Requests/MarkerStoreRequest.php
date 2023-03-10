<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkerStoreRequest extends FormRequest
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
			"position_x" => ["numeric"],
			"position_y" => ["numeric"],
			"web_position_x" => ["numeric"],
			"web_position_y" => ["numeric"],
            "target_x" => ["numeric"],
			"target_y" => ["numeric"],
			"target_height" => ["numeric"],
			"target_width" => ["numeric"],
            "scroll_x" => ["numeric"],
			"scroll_y" => ["numeric"],
			"screenshot_height" => ["numeric"],
			"screenshot_width" => ["numeric"],
			"device_pixel_ratio" => ["numeric"],
            "target_full_selector" => ["string"],
            "target_short_selector" => ["string"],
            "target_html" => ["string"]
		];
    }
}
