<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Comment;

class CommentService
{
    // Delete the comment
    public function delete($comment) 
    {
        $val = $comment->delete();

        return $val;
    }
}