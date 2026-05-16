<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->ajax()) {
            return view('admin.comments.index');
        }

        $comments = Comment::query();

        return Comment::dataTable($comments);
    }

    public function edit(Comment $comment)
    {
        return view('admin.comments.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        $input = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'text' => ['required', 'string'],
            'status' => ['required', 'numeric'],
        ]);
        $comment->update($input);

        return $this->jsonSuccess('Comment updated successfully');
    }

    public function updateStatus(Request $request, Comment $comment)
    {
        $input = $request->validate([
            'status' => ['required', 'numeric'],
        ]);
        $comment->update($input);

        return $this->jsonSuccess('Comment status updated successfully');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return $this->jsonSuccess('Comment deleted successfully');
    }
}
