<?php

namespace Tests\Feature\Comments;

use App\Http\Livewire\AddComment;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class AddCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function add_comment_livewire_component_renders(): void
    {
        $idea = Idea::factory()->create();
    
        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('add-comment');
    }

    /** @test */
    public function add_comment_form_renders_when_user_is_logged_in(): void
    {
        $idea = Idea::factory()->create();
    
        $this->actingAs($idea->user)
            ->get(route('idea.show', $idea))
            ->assertSee('Share your thoughts.');
    }

    /** @test */
    public function add_comment_form_does_NOT_render_when_user_is_NOT_logged_in(): void
    {
        $idea = Idea::factory()->create();
    
        $this->get(route('idea.show', $idea))
            ->assertDontSee('Share your thoughts.')
            ->assertSee('Please login or create an account to post a comment');
    }

    /** @test */
    public function add_comment_form_validation_works(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        Livewire::actingAs($user)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', '')
            ->call('addComment')
            ->assertHasErrors(['comment'])
            ->set('comment', '<4')
            ->call('addComment')
            ->assertHasErrors(['comment']);
    }

    /** @test */
    public function add_comment_form_works(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        Livewire::actingAs($user)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', 'A valid comment')
            ->call('addComment')
            ->assertHasNoErrors(['comment'])
            ->assertEmitted('commentWasAdded');
        
        $this->assertEquals(1, Comment::count());
        $this->assertEquals('A valid comment', $idea->comments->first()->body);
    }
}
