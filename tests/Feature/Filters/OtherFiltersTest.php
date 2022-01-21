<?php

namespace Tests\Feature;

use App\Http\Livewire\IdeasIndex;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class OtherFiltersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function top_voted_filters_works(): void
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();
        $userC = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $ideaOne->id,
            'user_id' => $user->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $ideaOne->id,
            'user_id' => $userB->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $ideaTwo->id,
            'user_id' => $userC->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'Top Voted')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->votes()->count() === 2
                    && $ideas->get(1)->votes()->count() === 1; // second idea
            });
    }

    /** @test */
    public function my_ideas_filter_works_correctly_when_user_logged_in(): void
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Second Idea'
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $userB->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        Livewire::actingAs($user)
            ->test(IdeasIndex::class)
            ->set('filter', 'My Ideas')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2 
                    && $ideas->first()->title === 'My Second Idea'
                    && $ideas->last()->title === 'My First Idea';
            });
    }

    /** @test */
    public function my_ideas_filter_works_correctly_when_user_NOT_logged_in(): void
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Second Idea'
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $userB->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'My Ideas')
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function my_ideas_filter_works_correctly_with_categories_filter(): void
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Second Idea'
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Third Idea'
        ]);

        Livewire::actingAs($user)
            ->test(IdeasIndex::class)
            ->set('filter', 'My Ideas')
            ->set('category', 'Category 1')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2 
                    && $ideas->first()->title === 'My Second Idea'
                    && $ideas->last()->title === 'My First Idea';
            });
    }

    /** @test */
    public function no_filter_works_correctly(): void
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Second Idea'
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $userB->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Third Idea'
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'No Filter')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 3
                    && $ideas->first()->title === 'My Third Idea' // latest
                    && $ideas->last()->title === 'My First Idea';
            });
    }

    /** @test */
    public function spam_ideas_filter_works_correctly(): void
    {
        $admin_user = User::factory()->admin()->create();

        $ideaOne = Idea::factory()->create(['spam_reports' => 4]);
        $ideaTwo = Idea::factory()->create(['spam_reports' => 3]);
        $ideaThree = Idea::factory()->create(['spam_reports' => 2]);
        $ideaNotSpam = Idea::factory()->create();

        Livewire::actingAs($admin_user)
            ->test(IdeasIndex::class)
            ->set('filter', 'Spam Ideas')
            ->assertViewHas('ideas', function ($ideas) use ($ideaOne, $ideaTwo, $ideaThree) {
                return $ideas->count() === 3
                && $ideas->first()->title === $ideaOne->title // highest spam_report count
                && $ideas->get(1)->title === $ideaTwo->title
                && $ideas->last()->title === $ideaThree->title;
            });
    }

    /** @test */
    public function spam_comments_filter_works_correctly(): void
    {
        $admin_user = User::factory()->admin()->create();

        $ideaOne = Idea::factory()->create();
        $ideaTwo = Idea::factory()->create();
        $ideaThree = Idea::factory()->create();

        Comment::factory()->for($ideaTwo)->create(['spam_reports' => 1]);
        Comment::factory()->for($ideaThree)->create(['spam_reports' => 1]);

        Livewire::actingAs($admin_user)
        ->test(IdeasIndex::class)
        ->set('filter', 'Spam Comments')
        ->assertViewHas('ideas', function ($ideas) use ($ideaOne) {
            return $ideas->count() === 2
            && !$ideas->contains($ideaOne);
        });
    }
}
