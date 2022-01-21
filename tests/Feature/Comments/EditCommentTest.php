<?php

namespace Tests\Feature\Comments;

use App\Http\Livewire\EditComment;
use App\Http\Livewire\IdeaComment;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_comment_idea_livewire_component_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('edit-comment');
    }

    /** @test */
    public function does_NOT_show_edit_comment_livewire_component_when_user_does_NOT_have_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('edit-comment');
    }

    /** @test */
    public function edit_comment_is_set_correctly_when_user_clicks_it_from_menu()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($user)->for($idea)->create();

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->assertSet('body', $comment->body)
            ->assertEmitted('editCommentWasSet');
    }

    /** @test */
    public function edit_comment_form_validation_works(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($user)->for($idea)->create();

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->set('body', '')
            ->call('updateComment')
            ->assertHasErrors(['body'])
            ->set('body', 'ab')
            ->call('updateComment')
            ->assertHasErrors(['body']);
    }

    /** @test */
    public function editing_a_comment_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($user)->for($idea)->create();

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->set('body', 'an UPDATED comment')
            ->call('updateComment');

        $this->assertEquals('an UPDATED comment', Comment::first()->body);
    }

    /** @test */
    public function editing_an_idea_does_NOT_work_when_user_does_NOT_have_authorization_because_different_user_created_the_comment(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($idea)->create();

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->set('body', 'an UPDATED comment')
            ->call('updateComment')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function editing_a_comment_shows_on_menu_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($idea)->for($user)->create();

        Livewire::actingAs($user)
            ->test(IdeaComment::class, [
                'comment' => $comment,
                'ideaUserId' => $idea->user_id,
            ])
            ->assertSee('Edit Comment');
    }

    /** @test */
    public function editing_a_comment_does_NOT_show_on_menu_when_user_has_NO_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($idea)->create();

        Livewire::actingAs($user)
            ->test(IdeaComment::class, [
                'comment' => $comment,
                'ideaUserId' => $idea->user_id,
            ])
            ->assertDontSee('Edit Comment');
    }
}
