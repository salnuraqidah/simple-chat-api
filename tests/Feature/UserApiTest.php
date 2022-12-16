<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    protected $loginData = ['email' => 'salma@sal.com', 'password' => 'salamah2022'];

    public function testStoreChat()
    {
        $response = $this->call('POST', '/api/login', $this->loginData);
        $token = $response->getData()->data->token;

        $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/chat/2')
            ->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',

                    ],
                    [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                ]
            ]);
    }
}
