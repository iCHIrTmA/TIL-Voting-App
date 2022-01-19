<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkAsNotSpam extends Component
{
    public $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function markNotSpam()
    {
        if(auth()->guest() || ! auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->idea->spam_reports = 0;
        $this->idea->save();

        $this->emit('ideaWasMarkedNotSpam', 'Spam counter was reset');
    }

    public function render()
    {
        return view('livewire.mark-as-not-spam');
    }
}
