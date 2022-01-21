<?php

namespace Tests\Feature\Comments;

use App\Http\Livewire\DeleteComment;
use App\Http\Livewire\IdeaComment;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_delete_idea_livewire_component_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('delete-comment');
    }

    /** @test */
    public function does_NOT_show_delete_comment_livewire_component_when_user_does_NOT_have_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('delete-comment');
    }

    /** @test */
    public function edit_comment_is_set_correctly_when_user_clicks_it_from_menu()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($user)->for($idea)->create();

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->assertEmitted('deleteCommentWasSet');
    }

    /** @test */
    public function deleting_a_comment_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($user)->for($idea)->create();

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->call('deleteComment');

        $this->assertEquals(0, Comment::count());
    }

    /** @test */
    public function deleting_an_idea_does_NOT_work_when_user_does_NOT_have_authorization_because_different_user_created_the_comment(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($idea)->create();

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->call('deleteComment')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function deleting_a_comment_shows_on_menu_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($idea)->for($user)->create();

        Livewire::actingAs($user)
            ->test(IdeaComment::class, [
                'comment' => $comment,
                'ideaUserId' => $idea->user_id,
            ])
            ->assertSee('Delete Comment');
    }

    /** @test */
    public function deleting_a_comment_does_NOT_show_on_menu_when_user_has_NO_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->for($idea)->create();

        Livewire::actingAs($user)
            ->test(IdeaComment::class, [
                'comment' => $comment,
                'ideaUserId' => $idea->user_id,
            ])
            ->assertDontSee('Delete Comment');
    }
}
