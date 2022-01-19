<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowIdeasTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function list_of_ideas_show_on_main_page(): void
    {
        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'OpenUnique']);
        $statusConsidering = Status::factory()->create(['name' => 'ConsideringUnique']);

        $ideaOne = Idea::factory()->create([
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);
        $ideaTwo = Idea::factory()->create([
            'category_id' => $categoryTwo->id,
            'status_id' => $statusConsidering->id,
        ]);

        $response = $this->get(route('idea.index'));

        $response->assertSuccessful();

        $response->assertSee($ideaOne->title);
        $response->assertSee($ideaOne->description);
        $response->assertSee($categoryOne->name);
        $response->assertSee('OpenUnique');
        $response->assertSee($ideaTwo->title);
        $response->assertSee($ideaTwo->description);
        $response->assertSee($categoryTwo->name);
        $response->assertSee('ConsideringUnique');
    }

    /** @test */
    public function single_idea_shows_correctly_on_idea_show_page(): void
    {
        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'OpenUnique']);
        $statusConsidering = Status::factory()->create(['name' => 'ConsideringUnique']);

        $idea = Idea::factory()->create([
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);
        $ideaNotSee = Idea::factory()->create([
            'category_id' => $categoryOne->id,
            'status_id' => $statusConsidering->id,
        ]);

        $response = $this->get(route('idea.show', $idea));

        $response->assertSuccessful();
        
        $response->assertSee($idea->title);
        $response->assertSee($idea->description);
        $response->assertSee('OpenUnique');
        $response->assertDontSee($ideaNotSee->title);
        $response->assertDontSee($ideaNotSee->description);
        $response->assertDontSee('ConsideringUnique');

    }

    /** @test */
    public function ideas_pagination_works(): void
    {
        $oldestIdea = Idea::factory()->create();

        Idea::factory($oldestIdea->getPerPage())->create();

        $latestIdea = Idea::all()->last();

        // latest id comes first
        $response = $this->get('/');
        $response->assertSee($latestIdea->title);
        $response->assertDontSee($oldestIdea->title);

        $response = $this->get('/?page=2');
        $response->assertSee($oldestIdea->title);
        $response->assertDontSee($latestIdea->title);
    }

    /** @test */
    public function same_idea_title_different_slugs(): void
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering', 'classes' => 'bg-purple text-white']);

        $ideaOne = Idea::factory()->create([
            'title' => 'A duplicate idea',
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);
        $ideaTwo = Idea::factory()->create([
            'title' => 'A duplicate idea',
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id
        ]);

        // dump($ideaOne->slug, $ideaTwo->slug);

        $this->assertNotSame($ideaOne->slug, $ideaTwo->slug);
    }

    /** @test */
    public function in_app_back_button_when_index_page_visited_first(): void
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering', 'classes' => 'bg-purple text-white']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        $response = $this->get('/?category=Category%202&status=Considering');
        $response = $this->get(route('idea.show', $ideaOne));

        $this->assertStringContainsString('/?category=Category%202&status=Considering', $response['backUrl']);
    }

    /** @test */
    public function in_app_back_button_when_show_page_only_page_visited(): void
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering', 'classes' => 'bg-purple text-white']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        $response = $this->get(route('idea.show', $ideaOne));

        $this->assertEquals(route('idea.index'), $response['backUrl']);
    }
}