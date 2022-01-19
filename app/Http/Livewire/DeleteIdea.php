<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use App\Models\Vote;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class DeleteIdea extends Component
{
    public $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function deleteIdea()
    {
        if(auth()->guest() || auth()->user()->cannot('delete', $this->idea)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        Vote::where('idea_id', $this->idea->id)->delete();
        Comment::where('idea_id', $this->idea->id)->delete();

        $this->idea->delete();

        session()->flash('success_message', 'Idea was deleted.');

        return redirect(route('idea.index'));
    }

    public function render()
    {
        return view('livewire.delete-idea');
    }
}