<?php

namespace Tests\Feature;

use App\Http\Livewire\AddComment;
use App\Http\Livewire\CommentNotifications;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Livewire;
use Tests\TestCase;

class CommentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function comment_notifications_livewire_component_renders_when_user_logged_in(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('idea.index'));

        $response->assertSeeLivewire('comment-notifications');
    }

    /** @test */
    public function comment_notifications_livewire_component_does_NOT_render_when_user_NOT_logged_in(): void
    {
        $response = $this->get(route('idea.index'));

        $response->assertDontSeeLivewire('comment-notifications');
    }

    /** @test */
    public function notifications_show_for_logged_in_user()
    {
        $ideaOwner = User::factory()->create();
        $idea = Idea::factory()->for($ideaOwner)->create();

        $userACommenting = User::factory()->create();
        $userBCommenting = User::factory()->create();

        Livewire::actingAs($userACommenting)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', 'This is the first comment')
            ->call('addComment');

        Livewire::actingAs($userBCommenting)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', 'This is the second comment')
            ->call('addComment');

        // 2 comments made through livewire are created at same time, this line below changes 1 comment to be created 2 minutes before
        DatabaseNotification::first()->update(['created_at' => now()->subMinutes(2)]);

        Livewire::actingAs($ideaOwner)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->assertSeeInOrder(['This is the second comment', 'This is the first comment'])
            ->assertSet('notificationCount', 2);
    }

    /** @test */
    public function notification_count_greater_than_threshold_show_for_logged_in_user()
    {
        $ideaOwner = User::factory()->create();
        $idea = Idea::factory()->for($ideaOwner)->create();

        $userACommenting = User::factory()->create();

        $threshold = CommentNotifications::NOTIFICATION_THRESHOLD;

        foreach(range(1, $threshold + 1) as $item) {
            Livewire::actingAs($userACommenting)
                ->test(AddComment::class, [
                    'idea' => $idea
                    ])
                ->set('comment', 'This is comment no. ' . $item)
                ->call('addComment');
        }


        Livewire::actingAs($ideaOwner)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->assertSet('notificationCount', $threshold . '+')
            ->assertSee($threshold . '+');
    }

    /** @test */
    public function can_mark_all_notifications_as_read()
    {
        $ideaOwner = User::factory()->create();
        $idea = Idea::factory()->for($ideaOwner)->create();

        $userACommenting = User::factory()->create();
        $userBCommenting = User::factory()->create();

        
        Livewire::actingAs($userACommenting)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', 'This is the first comment')
            ->call('addComment');
            
            Livewire::actingAs($userBCommenting)
                ->test(AddComment::class, [
                    'idea' => $idea
                    ])
                ->set('comment', 'This is the second comment')
                ->call('addComment');
                
        $this->assertEquals(2, $ideaOwner->unreadNotifications()->count());

        Livewire::actingAs($ideaOwner)
            ->test(CommentNotifications::class)
            ->assertSet('notificationCount', 2)
            ->call('markAllAsRead')
            ->assertSet('notificationCount', null);

        $this->assertEquals(0, $ideaOwner->unreadNotifications()->count());
    }

    /** @test */
    public function can_mark_individual_notifications_as_read()
    {
        $ideaOwner = User::factory()->create();
        $idea = Idea::factory()->for($ideaOwner)->create();

        $userACommenting = User::factory()->create();
        $userBCommenting = User::factory()->create();

        Livewire::actingAs($userACommenting)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', 'This is the first comment')
            ->call('addComment');

        Livewire::actingAs($userBCommenting)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', 'This is the second comment')
            ->call('addComment');

        $this->assertEquals(2, $ideaOwner->unreadNotifications()->count());

        Livewire::actingAs($ideaOwner)
            ->test(CommentNotifications::class)
            ->assertSet('notificationCount', 2)
            ->call('markAsRead', DatabaseNotification::first()->id)
            ->assertRedirect(route('idea.show',[
                'idea' => $idea,
                'page' => 1,
            ]))
            ->call('getNotificationCount')
            ->assertSet('notificationCount', 1);

        $this->assertEquals(1, $ideaOwner->unreadNotifications()->count());
    }

    /** @test */
    public function notification_idea_deleted_redirects_to_idea_index_page()
    {
        $ideaOwner = User::factory()->create();
        $idea = Idea::factory()->for($ideaOwner)->create();

        $userACommenting = User::factory()->create();

        Livewire::actingAs($userACommenting)
            ->test(AddComment::class, [
                'idea' => $idea
                ])
            ->set('comment', 'This is the first comment')
            ->call('addComment');
            
        $idea->delete();

        Livewire::actingAs($ideaOwner)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->call('markAsRead', DatabaseNotification::first()->id)
            ->assertRedirect(route('idea.index'));
    }

    /** @test */
    public function notification_comment_deleted_redirects_to_idea_index_page()
    {
        $ideaOwner = User::factory()->create();
        $idea = Idea::factory()->for($ideaOwner)->create();

        $userACommenting = User::factory()->create();

        Livewire::actingAs($userACommenting)
        ->test(AddComment::class, [
            'idea' => $idea
            ])
            ->set('comment', 'This is the first comment')
            ->call('addComment');
            
        $idea->comments()->delete();

        Livewire::actingAs($ideaOwner)
            ->test(CommentNotifications::class)
            ->call('markAsRead', DatabaseNotification::first()->id)
            ->assertRedirect(route('idea.index'));
    }
}
