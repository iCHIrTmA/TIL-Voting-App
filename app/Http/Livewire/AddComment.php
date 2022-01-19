<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class AddComment extends Component
{
    public $idea;
    public $comment;
    protected $rules = [
        'comment' => 'required|min:4'
    ];

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function addComment()
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        $this->validate();

        $this->idea->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->comment,
        ]);

        $this->reset('comment');

        $this->emit('commentWasAdded', 'Comment was posted!');
    }

    public function render()
    {
        return view('livewire.add-comment');
    }
}
