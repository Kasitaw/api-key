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
            'key',
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
     * @param bool $status
     *
     * @return \Illuminate\Database\Eloquent\Model|string
     */
    public function generateNewKey(bool $status = true)
    {
        do {
            $flag = true;
            $apiKey = Str::random(80);

            if (!DB::table(config('api-key.table_name.api_keys'))->where('key', $apiKey)->exists()) {
                $flag = false;
            }
        } while ($flag);

        $apiKey = $this->api_keys()->create([
            'key' => $apiKey,
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
            'key',
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
     * Base function to set the status.
     *
     * @param bool $status
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
     * @param array $input
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
     * @param string $keyType
     * @param array $keyOrUuid
     * @param bool $status
     *
     * @return bool
     */
    private function setStatusToSome(string $keyType, array $keyOrUuid, bool $status = true)
    {
        $identifiers = $this->flatten($keyOrUuid);

        $this
            ->api_keys()
            ->whereIn($keyType, $this->flatten($identifiers))
            ->update([
                'status' => $status,
            ]);

        return true;
    }
}
