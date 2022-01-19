<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GravatarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_generate_gravatar_default_image_when_no_email_found_first_char_a(): void
    {
        $user = User::factory()->create([
            'email' => 'afakeemailstartingwithchara@fakeemail.com'
        ]);

        $gravatarUrl = $user->getAvatar();

        $this->assertEquals(
            'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-1.png',
            $gravatarUrl
        );

        $response = Http::get($user->getAvatar());

        $this->assertTrue($response->successful());
    }

    /** @test */
    public function user_can_generate_gravatar_default_image_when_no_email_found_first_char_z(): void
    {
        $user = User::factory()->create([
            'email' => 'zfakeemailstartingwithcharz@fakeemail.com'
        ]);

        $gravatarUrl = $user->getAvatar();

        $this->assertEquals(
            'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-26.png',
            $gravatarUrl
        );

        $response = Http::get($user->getAvatar());

        $this->assertTrue($response->successful());
    }

    /** @test */
    public function user_can_generate_gravatar_default_image_when_no_email_found_first_char_0(): void
    {
        $user = User::factory()->create([
            'email' => '0fakeemailstartingwithchar0@fakeemail.com'
        ]);

        $gravatarUrl = $user->getAvatar();

        $this->assertEquals(
            'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-27.png',
            $gravatarUrl
        );

        $response = Http::get($user->getAvatar());

        $this->assertTrue($response->successful());
    }

    /** @test */
    public function user_can_generate_gravatar_default_image_when_no_email_found_first_char_9(): void
    {
        $user = User::factory()->create([
            'email' => '9fakeemailstartingwithchar9@fakeemail.com'
        ]);

        $gravatarUrl = $user->getAvatar();

        $this->assertEquals(
            'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-36.png',
            $gravatarUrl
        );

        $response = Http::get($user->getAvatar());

        $this->assertTrue($response->successful());
    }
}
