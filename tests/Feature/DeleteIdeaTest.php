<?php

namespace Tests\Feature;

use App\Http\Livewire\DeleteIdea;
use App\Http\Livewire\IdeaShow;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteIdeaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_delete_idea_livewire_component_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('delete-idea');
    }

    /** @test */
    public function does_NOT_show_delete_idea_livewire_component_when_user_does_NOT_have_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('delete-idea');
    }

    /** @test */
    public function deleting_an_idea_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(DeleteIdea::class, [
                'idea' => $idea
            ])
            ->call('deleteIdea')
            ->assertRedirect(route('idea.index'));

        $this->assertEquals(0, Idea::count());
    }

    /** @test */
    public function deleting_an_idea_with_votes_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->for($user)->create();

        Vote::factory()->create([
            'user_id' => $user->id,
            'idea_id' => $idea->id,
        ]);

        Livewire::actingAs($user)
            ->test(DeleteIdea::class, [
                'idea' => $idea
            ])
            ->call('deleteIdea')
            ->assertRedirect(route('idea.index'));

        $this->assertEquals(0, Idea::count());
        $this->assertEquals(0, Vote::count());
    }

    /** @test */
    public function deleting_an_idea_with_comments_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->for($user)->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
        ]);

        Livewire::actingAs($user)
            ->test(DeleteIdea::class, [
                'idea' => $idea
            ])
            ->call('deleteIdea')
            ->assertRedirect(route('idea.index'));

        $this->assertEquals(0, Idea::count());
        $this->assertEquals(0, Comment::count());
    }

    /** @test */
    public function deleting_an_idea_works_when_user_is_admin(): void
    {
        $user = User::factory()->admin()->create();

        $idea = Idea::factory()->create(); // idea not created by user

        Livewire::actingAs($user)
            ->test(DeleteIdea::class, [
                'idea' => $idea
            ])
            ->call('deleteIdea')
            ->assertRedirect(route('idea.index'));

        $this->assertEquals(0, Idea::count());
    }

    /** @test */
    public function deleting_an_idea_shows_on_menu_when_user_has_validation(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertSee('Delete Idea');
    }

    /** @test */
    public function deleting_an_idea_does_NOT_show_on_menu_when_user_has_NO_validation(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertDontSee('Delete Idea');
    }
}
