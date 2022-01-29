<?php

namespace App\Http\Livewire;

use App\Jobs\NotifyAllVoters;
use App\Mail\IdeaStatusUpdatedMailable;
use App\Models\Comment;
use App\Models\Idea;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class SetStatus extends Component
{
    public $idea;
    public $status;
    public $comment;
    public $notifyAllVoters;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->status = $this->idea->status_id;
    }

    public function setStatus()
    {
        if(! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if($this->idea->status_id ===  (int) $this->status) {
            $this->emit('statusWasUpdatedError', 'The status is the same!');

            return;
        }

        $this->idea->status_id = $this->status;
        $this->idea->save();

        if($this->notifyAllVoters) {
            NotifyAllVoters::dispatch($this->idea);
        }

        $this->idea->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->comment,
            'status_id' => $this->status,
            'body' => $this->comment ?? 'An admin updated the status of this idea',
            'is_status_update' => true,
        ]);

        $this->emit('statusWasUpdated', 'Status successfully updated!');
    }

    public function render()
    {
        return view('livewire.set-status');
    }
}
