<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    protected $loginData = ['email' => 'salma@sal.com', 'password' => 'salamah2022'];

    public function testLoginTrue()
    {
        $this->json('POST', 'api/login', $this->loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "user" => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                     "token",
                     "token_type"
                ]
            ]);
    }

    public function testLoginFalse()
    {
        $loginData = ['email' => 'salma@sal.com', 'password' => 'salamah20221'];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                "status"=> false,
                "message" => "Unauthorized"
            ]);
    }

    public function testLogout()
    {
        $response = $this->call('POST', '/api/login',$this->loginData);
        $token = $response->getData()->data->token;
        
        $this->withHeader('Authorization','Bearer '.$token)->json('POST','api/logout')
            ->assertStatus(200)
            ->assertJson([
                "message" => "You have successfully logged out and the token was successfully deleted"
            ]);
    }
}
