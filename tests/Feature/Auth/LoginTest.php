<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_login_data_success()
    {
        $loginData = [
            'email' => 'developer@wangun.co',
            'password' => 'password',
        ];

        $response = app('DoLogin')->execute($loginData);

        $this->assertEquals(200, $response['response_code']);
        $this->assertNull($response['error']);
        $this->assertEquals('User successfully logged in', $response['message']);

        $user = User::where('email', $loginData['email'])->first();
        $this->assertEquals($user->uuid, $response['data']['user']['user_uuid']);
        $this->assertEquals($user->userRole->role->uuid, $response['data']['user']['role_uuid']);
        $this->assertEquals($user->userRole->role->name, $response['data']['user']['role_name']);
    }

    public function test_login_failed()
    {
        $loginData = [
            'email' => 'zzzz@wangun.co',
            'password' => 'password',
        ];
        $response = app('DoLogin')->execute($loginData);

        $this->assertNotEquals(200, $response['response_code']);
    }
}
