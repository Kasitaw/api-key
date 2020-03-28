<?php

namespace Kasitaw\ApiKey\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait HasApiKey
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function api_keys()
    {
        return $this->morphMany(
            config('api-key.model.api_key'),
            'authenticable',
            'model_type',
            'model_id'
        );
    }

    /**
     * Revoke all the existing keys.
     *
     * @return \Kasitaw\ApiKey\Traits\HasApiKey
     */
    public function revokeAllKeys()
    {
        return $this->setStatusToAll(false);
    }

    /**
     * Revoke certain key by "key" column in table.
     *
     * @param mixed ...$keys
     *
     * @return bool
     */
    public function revokeKeyByKey(...$keys)
    {
        return $this->setStatusToSome(
            config('api-key.columns.key') ?? 'key',
            $keys,
            false
        );
    }

    /**
     * Revoke certain key by "uuid" column in table.
     *
     * @param mixed ...$uuid
     *
     * @return bool
     */
    public function revokeKeyByUuid(...$uuid)
    {
        return $this->setStatusToSome(
            'uuid',
            $uuid,
            false
        );
    }

    /**
     * Generate new key.
     *
     * @return \Illuminate\Database\Eloquent\Model|string
     */
    public function generateNewKey(bool $status = true)
    {
        do {
            $flag = true;
            $apiKey = Str::random(config('api-key.key_length'));

            $existingKey = DB::table(config('api-key.table_name.api_keys'))
                ->where(config('api-key.columns.key') ?? 'key', $apiKey)
                ->exists();

            if (!$existingKey) {
                $flag = false;
            }
        } while ($flag);

        $apiKey = $this->api_keys()->create([
            config('api-key.columns.key') ?? 'key' => $apiKey,
            'status' => $status,
        ]);

        return $apiKey;
    }

    /**
     * Activate all the keys.
     *
     * @return \Kasitaw\ApiKey\Traits\HasApiKey
     */
    public function activateAllKeys()
    {
        return $this->setStatusToAll();
    }

    /**
     * Activate key using "key" column in table.
     *
     * @param mixed ...$keys
     *
     * @return bool
     */
    public function activateKeyByKey(...$keys)
    {
        return $this->setStatusToSome(
            config('api-key.columns.key') ?? 'key',
            $keys,
            true
        );
    }

    /**
     * Activate key using "uuid" column in table.
     *
     * @param mixed ...$uuid
     *
     * @return bool
     */
    public function activateKeyByUuid(...$uuid)
    {
        return $this->setStatusToSome(
            'uuid',
            $uuid,
            true
        );
    }

    /**
     * Delete all api keys.
     *
     * @return mixed
     */
    public function removeAllKeys()
    {
        return $this->api_keys()->delete();
    }

    /**
     * Delete key by "key".
     *
     * @param mixed ...$keys
     *
     * @return \Kasitaw\ApiKey\Traits\HasApiKey
     */
    public function removeKeyByKey(...$keys)
    {
        return $this->deleteKeys(
            config('api-key.columns.key') ?? 'key',
            $keys
        );
    }

    /**
     * Delete key by "uuid".
     *
     * @param mixed ...$uuid
     *
     * @return \Kasitaw\ApiKey\Traits\HasApiKey
     */
    public function removeKeyByUuid(...$uuid)
    {
        return $this->deleteKeys(
            'uuid',
            $uuid
        );
    }

    /**
     * Check whether key is active. Return null of "key" does not exist.
     *
     * @param $keyOrUuid
     *
     * @return bool|null
     */
    public function isKeyActive($keyOrUuid)
    {
        $key = $this
            ->api_keys()
            ->where(config('api-key.columns.key') ?? 'key', $keyOrUuid)
            ->orWhere('uuid', $keyOrUuid)
            ->first();

        return $key ? (true === $key->status) : null;
    }

    /**
     * Base function to set the status.
     *
     * @return $this
     */
    private function setStatusToAll(bool $status = true)
    {
        $this->api_keys()->update([
            'status' => $status,
        ]);

        return $this;
    }

    /**
     * Flatten the multi array into flat view.
     *
     * @return array
     */
    private function flatten(array $input)
    {
        return collect($input)->flatten()->all();
    }

    /**
     * Base method to set status(active or inactive) for some keys.
     *
     * @return bool
     */
    private function setStatusToSome(string $keyType, array $keyOrUuid, bool $status = true)
    {
        $identifiers = $this->flatten($keyOrUuid);

        $this
            ->api_keys()
            ->whereIn($keyType, $identifiers)
            ->update([
                'status' => $status,
            ]);

        return true;
    }

    /**
     * Base method to delete the key.
     *
     * @return $this
     */
    private function deleteKeys(string $keyType, array $keyOrUuid)
    {
        $identifiers = $this->flatten($keyOrUuid);

        $this
            ->api_keys()
            ->whereIn($keyType, $identifiers)
            ->delete();

        return $this;
    }
}
