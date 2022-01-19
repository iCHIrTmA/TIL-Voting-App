<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function idea_comments_livewire_component_renders(): void  
    {
        $idea = Idea::factory()->create();

        Comment::factory()->for($idea)->create();
    
        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('idea-comments');
    }

    /** @test */
    public function idea_comment_livewire_component_renders(): void  
    {
        $idea = Idea::factory()->create();

        Comment::factory()->for($idea)->create();
    
        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('idea-comment');
    }

    /** @test */
    public function no_comments_show_appropriate_message(): void  
    {
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('idea-comment')
            ->assertSee('No comments yet');
    }

    /** @test */
    public function list_of_comments_show_on_idea_page(): void  
    {
        $idea = Idea::factory()->create();

        $commentOne = Comment::factory()->for($idea)->create();
        $commentTwo = Comment::factory()->for($idea)->create();

        $this->get(route('idea.show', $idea))
            ->assertSeeInOrder([$commentOne->body, $commentTwo->body])
            ->assertSee('2 comments');
    }

    /** @test */
    public function comments_count_shows_correctly_on_index_page(): void  
    {
        $idea = Idea::factory()->create();

        $commentOne = Comment::factory()->for($idea)->create();
        $commentTwo = Comment::factory()->for($idea)->create();

        $this->get(route('idea.index'))
            ->assertSee('2 comments');
    }

    /** @test */
    public function op_badge_shows_if_author_of_idea_comments_on_his_own_idea(): void  
    {
        $user = User::factory()->create();
        
        $idea = Idea::factory()->for($user)->create();

        $commentOne = Comment::factory()->for($idea)->create();
        $commentTwo = Comment::factory()->for($user)->for($idea)->create();

        $response = $this->get(route('idea.show', $idea));

        $this->assertEquals($idea->user_id, $commentTwo->user_id);
        $response->assertSee('2 comments');
        $response->assertSee('OP');
    }

    /** @test */
    public function comments_pagination_works(): void
    {
        $idea = Idea::factory()->create();

        $oldestComment = Comment::factory()->for($idea)->create();

        Comment::factory($oldestComment->getPerPage())->for($idea)->create();

        $latestComment = Comment::all()->last();

        // oldest comment id comes first
        $response = $this->get(route('idea.show', $idea));
        $response->assertSee($oldestComment->body);
        $response->assertDontSee($latestComment->body);

        $response = $this->get(route('idea.show', [
            'idea' => $idea,
            'page' => 2
        ]));

        $response->assertSee($latestComment->body);
        $response->assertDontSee($oldestComment->body);
    }
}
