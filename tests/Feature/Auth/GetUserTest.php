<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_all_user_success()
    {
        $response = app('GetUserService')->execute([]);

        $this->assertEquals(200, $response['response_code']);
    }

    public function test_get_spesific_user_success()
    {
        $role = Role::find(1);

        $response = app('GetUserService')->execute([
            'role_uuid' => $role->uuid
        ]);

        $this->assertEquals(200, $response['response_code']);
    }
}
