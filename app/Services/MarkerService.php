<?php

namespace App\Services;
use Illuminate\Support\Str;

class MarkerService
{
    // Store a newly created marker on the server.
    public function store($screenshot, $marker)
    {
        // Create a new marker
		$marker = $screenshot->markers()->create([
            "id" => (string) Str::uuid(),
            "screenshot_id" => $screenshot->id,
            "position_x" => $marker->position_x,
            "position_y" => $marker->position_y,
            "web_position_x" => $marker->web_position_x,
            "web_position_y" => $marker->web_position_y,
            "target_x" => $marker->target_x,
            "target_y" => $marker->target_y,
            "target_height" => $marker->target_height,
            "target_width" => $marker->target_width,
            "scroll_x" => $marker->scroll_x,
            "scroll_y" => $marker->scroll_y,
            "screenshot_height" => $marker->screenshot_height,
            "screenshot_width" => $marker->screenshot_width,
            "target_full_selector" => $marker->target_full_selector,
            "target_short_selector" => $marker->target_short_selector,
            "target_html" => $marker->target_html,
		]);

        return $marker;
    }

    // Delete the marker
    public function delete($marker) 
    {
        $val = $marker->delete();

        return $val;
    }
}