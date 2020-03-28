<?php

namespace Kasitaw\ApiKey\Tests\Unit;

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

    public function test_can_generate_api_key()
    {
        $apiKey = $this->user->generateNewKey();

        $this->assertDatabaseHas('api_keys', ['key' => $apiKey->key]);
    }

    public function test_can_revoke_all_api_keys()
    {
        factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->revokeAllKeys();

        $this->user->api_keys->each(function ($item) {
            $this->assertFalse($item->status);
        });
    }

    public function test_can_revoke_key_by_key_with_single_key()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->revokeKeyByKey($apiKey->key);

        $this->assertDatabaseHas('api_keys', [
            'uuid' => $apiKey->uuid,
            'key' => $apiKey->key,
            'status' => false,
        ]);
    }

    public function test_can_revoke_key_by_key_with_multiple_key()
    {
        $apiKeys = factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->revokeKeyByKey($apiKeys[0]->key, $apiKeys[1]->key, $apiKeys[2]->key);

        $apiKeys->each(function ($item) {
            $this->assertDatabaseHas('api_keys', [
                'uuid' => $item->uuid,
                'key' => $item->key,
                'status' => false,
            ]);
        });
    }

    public function test_can_revoke_key_by_uuid_with_single_uuid()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->revokeKeyByUuid($apiKey->uuid);

        $this->assertDatabaseHas('api_keys', [
            'uuid' => $apiKey->uuid,
            'key' => $apiKey->key,
            'status' => false,
        ]);
    }

    public function test_can_revoke_key_by_uuid_with_multiple_uuid()
    {
        $apiKeys = factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->revokeKeyByUuid($apiKeys[0]->uuid, $apiKeys[1]->uuid, $apiKeys[2]->uuid);

        $apiKeys->each(function ($item) {
            $this->assertDatabaseHas('api_keys', [
                'uuid' => $item->uuid,
                'key' => $item->key,
                'status' => false,
            ]);
        });
    }

    public function test_can_activate_all_api_keys()
    {
        factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->activateAllKeys();

        $this->user->api_keys->each(function ($item) {
            $this->assertTrue($item->status);
        });
    }

    public function test_can_activate_key_by_key_with_single_key()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->activateKeyByKey($apiKey->key);

        $this->assertDatabaseHas('api_keys', [
            'uuid' => $apiKey->uuid,
            'key' => $apiKey->key,
            'status' => true,
        ]);
    }

    public function test_can_activate_key_by_key_with_multiple_key()
    {
        $apiKeys = factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->activateKeyByKey($apiKeys[0]->key, $apiKeys[1]->key, $apiKeys[2]->key);

        $apiKeys->each(function ($item) {
            $this->assertDatabaseHas('api_keys', [
                'uuid' => $item->uuid,
                'key' => $item->key,
                'status' => true,
            ]);
        });
    }

    public function test_can_activate_key_by_uuid_with_single_uuid()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->activateKeyByUuid($apiKey->uuid);

        $this->assertDatabaseHas('api_keys', [
            'uuid' => $apiKey->uuid,
            'key' => $apiKey->key,
            'status' => true,
        ]);
    }

    public function test_can_activate_key_by_uuid_with_multiple_uuid()
    {
        $apiKeys = factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->activateKeyByUuid($apiKeys[0]->uuid, $apiKeys[1]->uuid, $apiKeys[2]->uuid);

        $apiKeys->each(function ($item) {
            $this->assertDatabaseHas('api_keys', [
                'uuid' => $item->uuid,
                'key' => $item->key,
                'status' => true,
            ]);
        });
    }

    public function test_can_remove_all_api_keys()
    {
        $apiKeys = factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->removeAllKeys();

        $apiKeys->each(function ($item) {
            $this->assertSoftDeleted('api_keys', [
                'uuid' => $item->uuid,
                'key' => $item->key,
            ]);
        });
    }

    public function test_can_remove_api_key_with_key_with_single_key()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->removeKeyByKey($apiKey->key);

        $this->assertSoftDeleted('api_keys', [
            'uuid' => $apiKey->uuid,
            'key' => $apiKey->key,
        ]);
    }

    public function test_can_remove_api_key_with_key_with_multiple_key()
    {
        $apiKeys = factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->removeKeyByKey($apiKeys[0]->key, $apiKeys[1]->key, $apiKeys[2]->key);

        $apiKeys->each(function ($item) {
            $this->assertSoftDeleted('api_keys', [
                'uuid' => $item->uuid,
                'key' => $item->key,
            ]);
        });
    }

    public function test_can_remove_api_key_with_uuid_with_single_uuid()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->removeKeyByUuid($apiKey->uuid);

        $this->assertSoftDeleted('api_keys', [
            'uuid' => $apiKey->uuid,
            'key' => $apiKey->key,
        ]);
    }

    public function test_can_remove_api_key_with_uuid_with_multiple_uuid()
    {
        $apiKeys = factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $this->user->removeKeyByUuid($apiKeys[0]->uuid, $apiKeys[1]->uuid, $apiKeys[2]->uuid);

        $apiKeys->each(function ($item) {
            $this->assertSoftDeleted('api_keys', [
                'uuid' => $item->uuid,
                'key' => $item->key,
            ]);
        });
    }

    public function test_can_check_is_api_key_is_active_using_uuid()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $actual = $this->user->isKeyActive($apiKey->uuid);

        $this->assertTrue($actual, true);
    }

    public function test_can_check_is_api_key_is_active_using_key()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        $actual = $this->user->isKeyActive($apiKey->key);

        $this->assertTrue($actual, true);
    }

    public function test_can_check_is_api_key_is_inactive_using_uuid()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
            'status' => false,
        ]);

        $actual = $this->user->isKeyActive($apiKey->uuid);

        $this->assertFalse($actual, false);
    }

    public function test_can_check_is_api_key_is_inactive_using_key()
    {
        $apiKey = factory(ApiKey::class)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
            'status' => false,
        ]);

        $actual = $this->user->isKeyActive($apiKey->key);

        $this->assertFalse($actual, false);
    }

    public function test_can_get_all_keys()
    {
        factory(ApiKey::class, 3)->create([
            'model_type' => get_class($this->user),
            'model_id' => $this->user->getKey(),
        ]);

        factory(ApiKey::class, 4)->create();

        $this->assertSame($this->user->api_keys->count(), 3);
    }
}
