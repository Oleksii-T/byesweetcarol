<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine if the given post can be updated by the user.
     */
    public function update(?User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isWriter() && $user->id == $post->user_id) {
            return true;
        }

        return false;
    }
}
