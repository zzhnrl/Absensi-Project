<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_submit_data_succes()
    {
        $role = Role::find(1);

        $userData = [
            'email' => 'nrzh@gmail.com',
            'password' => 'password',
            'role_uuid' => $role->uuid,
            'nama' => 'Azizahh',
            'notlp' => '081354624598',
            'alamat' => 'Subang'
        ];

        $response = app('RegisterNewUserService')->execute($userData);

        $this->assertEquals(200, $response['response_code']);
        $this->assertNull($response['error']);
        $this->assertEquals('User successfully stored', $response['message']);

        $this->assertEquals($userData['email'], $response['data']['email']);
    }

    public function test_submit_data_nama_not()
    {
        $role = Role::find(1);

        $userData = [
            'email' => 'nrlzzhh@gmail.com',
            'password' => 'password',
            'role_uuid' => $role->uuid,
            'nama' => 'Azizah',
            'notlp' => '081354624589',
            'alamat' => 'Subangg'
        ];

        $response = app('RegisterNewUserService')->execute($userData);

        $this->assertNotEquals(200, $response['response_code']);
    }
}
