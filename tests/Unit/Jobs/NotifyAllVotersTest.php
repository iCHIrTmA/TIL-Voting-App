<?php

namespace Tests\Unit\Jobs;

use App\Jobs\NotifyAllVoters;
use App\Mail\IdeaStatusUpdatedMailable;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotifyAllVotersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_email_to_all_voters()
    {
        $user = User::factory()->create();
        $secondUser = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $idea = Idea::factory()->create([
            'title' => 'An idea',
            'description' => 'brief description for this idea',
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $secondUser->id,
        ]);

        Mail::fake();

        NotifyAllVoters::dispatch($idea);

        Mail::assertQueued(IdeaStatusUpdatedMailable::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email)
                && $mail->build()->subject === 'An idea you voted for changed its status';
        });

        Mail::assertQueued(IdeaStatusUpdatedMailable::class, function ($mail) use ($secondUser) {
            return $mail->hasTo($secondUser->email)
                && $mail->build()->subject === 'An idea you voted for changed its status';
        });
    }
}
