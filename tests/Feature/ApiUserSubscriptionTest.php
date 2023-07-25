<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiUserSubscriptionTest extends TestCase
{
    public function test_register_route(): void
    {
        $params = ['name' => 'Meysam', 'email' => 'meysam.alavi@gmail.com', 'password' => '12345600'];
        $response = $this->post('/api/user/register', $params);

        $response->assertStatus(200);
    }

    public function test_register_route_method(): void
    {
        $params = ['name' => 'Meysam', 'email' => 'meysam.alavi@gmail.com', 'password' => '12345600'];
        $response = $this->get('/api/user/register', $params);

        $response->assertNotFound();
    }
}
