<?php

namespace Tests\Feature;

use App\Http\Livewire\IdeaIndex;
use App\Http\Livewire\IdeaShow;
use App\Http\Livewire\MarkAsNotSpam;
use App\Http\Livewire\MarkAsSpam;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SpamManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_mark_spam_livewire_component_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('mark-as-spam');
    }

    /** @test */
    public function does_NOT_show_mark_as_spam_component_when_user_does_NOT_have_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('mark-as-spam');
    }

    /** @test */
    public function marking_an_idea_as_spam_works_when_user_has_authorization(): void
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(MarkAsSpam::class, [
                'idea' => $idea
            ])
            ->call('markSpam')
            ->assertEmitted('ideaWasMarkedAsSpam');

        $this->assertEquals($idea->fresh()->spam_reports, 1);
    }

    /** @test */
    public function marking_an_idea_as_spam_does_NOT_work_when_user_is_NOT_logged_in(): void
    {
        $idea = Idea::factory()->create();

        Livewire::test(MarkAsSpam::class, [
                'idea' => $idea
            ])
            ->call('markSpam')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function mark_as_spam_option_shows_on_menu_when_user_has_authorization(): void
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertSee('Mark as Spam');
    }

    /** @test */
    public function mark_as_spam_option_does_NOT_show_on_menu_when_user_is_NOT_logged_in(): void
    {
        $idea = Idea::factory()->create();

        Livewire::test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertDontSee('Mark as Spam');
    }

    /** @test */
    public function shows_mark_not_spam_livewire_component_when_user_has_authorization()
    {
        $admin_user = User::factory()->admin()->create();
        $idea = Idea::factory()->create([
            'user_id' => $admin_user->id
        ]);

        $this->actingAs($admin_user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('mark-as-not-spam');
    }

    /** @test */
    public function does_NOT_show_mark_as_not_spam_livewire_component_when_user_does_NOT_have_authorization()
    {
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('mark-as-not-spam');
    }

    /** @test */
    public function marking_an_idea_as_not_spam_works_when_user_has_authorization(): void
    {
        $admin_user = User::factory()->admin()->create();

        $idea = Idea::factory()->for($admin_user)->create();

        Livewire::actingAs($admin_user)
            ->test(MarkAsNotSpam::class, [
                'idea' => $idea
            ])
            ->call('markNotSpam')
            ->assertEmitted('ideaWasMarkedNotSpam');

        $this->assertNotEquals($idea->fresh()->spam_reports, 1);
    }

    /** @test */
    public function marking_an_idea_as_not_spam_does_NOT_work_when_user_is_NOT_logged_in(): void
    {
        $idea = Idea::factory()->create();

        Livewire::test(MarkAsNotSpam::class, [
                'idea' => $idea
            ])
            ->call('markNotSpam')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function mark_as_not_spam_option_shows_on_menu_when_user_has_authorization(): void
    {
        $admin_user = User::factory()->admin()->create();
        $idea = Idea::factory()->for($admin_user)->create();

        Livewire::actingAs($admin_user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertSee('Not Spam');
    }

    /** @test */
    public function mark_as_not_spam_option_does_NOT_show_on_menu_when_user_is_NOT_logged_in(): void
    {
        $idea = Idea::factory()->create();

        Livewire::test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertDontSee('Not Spam');
    }

    /** @test */
    public function spam_reports_count_shows_on_idea_index_page_if_logged_in_as_admin(): void
    {
        $admin_user = User::factory()->admin()->create();

        $idea = Idea::factory()->for($admin_user)->create([
            'spam_reports' => 2,
        ]);

        Livewire::actingAs($admin_user)
            ->test(IdeaIndex::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertSee('Spam Reports: 2');
    }

    /** @test */
    public function spam_reports_count_shows_on_idea_show_page_if_logged_in_as_admin(): void
    {
        $admin_user = User::factory()->admin()->create();

        $idea = Idea::factory()->for($admin_user)->create([
            'spam_reports' => 2,
        ]);

        Livewire::actingAs($admin_user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 4,
            ])
            ->assertSee('Spam Reports: 2');
    }
}
