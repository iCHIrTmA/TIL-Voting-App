<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkCommentNotSpam extends Component
{
    public Comment $comment;

    protected $listeners = ['setMarkNotSpamComment'];

    public function setMarkNotSpamComment($commentId)
    {
        $this->comment = Comment::findOrFail($commentId);

        $this->emit('markNotSpamCommentWasSet');
    }

    public function markNotSpam()
    {
        if(auth()->guest() || ! auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->comment->spam_reports = 0;
        $this->comment->save();

        $this->emit('commentWasMarkedNotSpam', 'Comment spam counter was reset!');
    }
    
    public function render()
    {
        return view('livewire.mark-comment-not-spam');
    }
}
