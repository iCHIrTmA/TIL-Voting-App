<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_check_if_user_is_an_admin()
    {
        $userAdmin = User::factory()->make([
            'email' => 'admin_dec_30_2021@example.net'
        ]);

        $userNormal = User::factory()->make();

        $this->assertTrue($userAdmin->isAdmin());
        $this->assertFalse($userNormal->isAdmin());
    }

}
