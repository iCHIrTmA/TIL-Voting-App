<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use Livewire\Component;
use Livewire\WithPagination;

class IdeaComments extends Component
{
    use WithPagination;

    public $idea;
    protected $listeners = ['commentWasAdded'];

    public function commentWasAdded()
    {
        $this->idea->refresh();
        $this->goToPage($this->idea->comments()->paginate()->lastPage());
    }

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function render()
    {
        return view('livewire.idea-comments', [
            'comments' => $this->idea->comments()->with(['user'])->paginate()->withQueryString(),
        ]);
    }
}