<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkAsSpam extends Component
{
    public $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function markSpam()
    {
        if(auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->idea->spam_reports++;
        $this->idea->save();

        $this->emit('ideaWasMarkedAsSpam', 'Idea was marked spam!');
    }

    public function render()
    {
        return view('livewire.mark-as-spam');
    }
}
