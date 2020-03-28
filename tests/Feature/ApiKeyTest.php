<?php

namespace Kasitaw\ApiKey\Tests\Feature;

use Kasitaw\ApiKey\ApiKey;
use Kasitaw\ApiKey\Tests\TestCase;
use Kasitaw\ApiKey\Tests\TestModel\User;

/**
 * Class ApiKeyTest.
 *
 * @property User $user
 */
class ApiKeyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    public function test_can_access_route_using_active_api_key_with_authorization_header()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$apiKey->key}",
        ])->json('GET', '/authorize/user');

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
            ]);
    }

    public function test_can_access_route_using_active_api_key_with_query_param()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', "/authorize/user?api_key={$apiKey->key}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
            ]);
    }

    public function test_can_access_route_using_active_api_key_with_http_body()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/authorize/user', [
            'api_key' => $apiKey->key,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
            ]);
    }

    public function test_cannot_access_route_using_inactive_api_key_with_authorization_header()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
            'status' => false,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$apiKey->key}",
        ])->json('GET', '/unauthorized/user');

        $response
            ->assertStatus(401);
    }

    public function test_cannot_access_route_using_inactive_api_key_with_query_param()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
            'status' => false,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', "/unauthorized/user?api_key={$apiKey->key}");

        $response
            ->assertStatus(401);
    }

    public function test_cannot_access_route_using_inactive_api_key_with_http_body()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
            'status' => false,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/unauthorized/user', [
            'api_key' => $apiKey->key,
        ]);

        $response
            ->assertStatus(401);
    }

    public function test_cannot_access_route_if_not_passing_api_key()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/unauthorized/user');

        $response
            ->assertStatus(401);
    }
}
