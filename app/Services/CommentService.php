<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Comment;

class CommentService
{
    // Delete the comment
    public function delete($comment) 
    {
        $val = $comment->update([
            "deleted_at" => new \DateTime()
        ]);

        return $val;
    }
}