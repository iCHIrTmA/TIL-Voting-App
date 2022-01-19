<?php

namespace Tests\Feature;

use App\Http\Livewire\CreateIdea;
use App\Http\Livewire\EditIdea;
use App\Http\Livewire\IdeaShow;
use App\Models\Category;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditIdeaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_edit_idea_livewire_component_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('edit-idea');
    }

    /** @test */
    public function does_NOT_show_edit_idea_livewire_component_when_user_does_NOT_have_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('edit-idea');
    }

    /** @test */
    public function edit_idea_form_validation_works(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id
        ]);

        Livewire::actingAs($user)
            ->test(EditIdea::class, [
                'idea' => $idea
            ])
            ->set('title', '')
            ->set('category', '')
            ->set('description', '')
            ->call('updateIdea')
            ->assertHasErrors(['title', 'category', 'description'])
            ->assertSee('The title field is required');
    }

    /** @test */
    public function editing_an_idea_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne,
        ]);

        Livewire::actingAs($user)
            ->test(EditIdea::class, [
                'idea' => $idea
            ])
            ->set('title', 'UPDATED TITLE')
            ->set('category', $categoryTwo->id)
            ->set('description', 'UPDATED DESCRIPTION')
            ->call('updateIdea')
            ->assertHasNoErrors(['title', 'category', 'description'])
            ->assertEmitted('ideaWasUpdated');

        $this->assertDatabaseHas('ideas', [
            'title' => 'UPDATED TITLE',
            'description' => 'UPDATED DESCRIPTION',
            'category_id' => $categoryTwo->id,
        ]);
    }

    /** @test */
    public function editing_an_idea_does_NOT_work_when_user_does_NOT_have_authorization_because_different_user_created_the_idea(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $idea = Idea::factory()->create([
            'user_id' => $userA->id,
            'category_id' => $categoryOne,
        ]);

        Livewire::actingAs($userB)
            ->test(EditIdea::class, [
                'idea' => $idea
            ])
            ->set('title', 'UPDATED TITLE')
            ->set('category', $categoryTwo->id)
            ->set('description', 'UPDATED DESCRIPTION')
            ->call('updateIdea')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function editing_an_idea_does_NOT_work_when_user_does_NOT_have_authorization_because_idea_was_created_more_than_an_hours_ago(): void
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne,
            'created_at' => now()->subHours(2)
        ]);

        Livewire::actingAs($user)
            ->test(EditIdea::class, [
                'idea' => $idea
            ])
            ->set('title', 'UPDATED TITLE')
            ->set('category', $categoryTwo->id)
            ->set('description', 'UPDATED DESCRIPTION')
            ->call('updateIdea')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function editing_an_idea_shows_on_menu_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id
        ]);

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertSee('Edit Idea');
    }

    /** @test */
    public function editing_an_idea_does_NOT_show_on_menu_when_user_has_NO_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertDontSee('Edit Idea');
    }
}
