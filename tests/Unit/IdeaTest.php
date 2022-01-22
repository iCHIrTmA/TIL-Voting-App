<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdeaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_if_idea_is_voted_for_by_user()
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

        $this->assertTrue($idea->isVotedByUser($user));
        $this->assertFalse($idea->isVotedByUser($secondUser));
        $this->assertFalse($idea->isVotedByUser(null));
    }

    /** @test */
    public function idea_can_be_voted_by_user()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $idea = Idea::factory()->create([
            'title' => 'An idea',
            'description' => 'brief description for this idea',
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        $this->assertFalse($idea->isVotedByUser($user));
        $idea->vote($user);
        $this->assertTrue($idea->isVotedByUser($user));
    }

    /** @test */
    public function idea_can_be_UNvoted_by_user()
    {
        $user = User::factory()->create();

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
        
        $this->assertTrue($idea->isVotedByUser($user));
        $idea->removeVote($user);
        $this->assertFalse($idea->isVotedByUser($user));
    }
}
