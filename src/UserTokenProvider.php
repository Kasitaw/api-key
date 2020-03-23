<?php

namespace Kasitaw\ApiKey;

use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class UserTokenProvider implements UserProvider
{
    private $apiKey;
    private $user;

    public function __construct(User $user, ApiKey $apiKey)
    {
        $this->user = $user;
        $this->apiKey = $apiKey;
    }

    public function retrieveById($identifier)
    {
        return $this->user->find($identifier);
    }

    public function retrieveByToken($identifier, $apiKey)
    {
        return $this->apiKey
            ->with('authenticable')
            ->where($identifier, $apiKey)
            ->first();
    }

    public function updateRememberToken(Authenticatable $user, $apiKey)
    {
        // update via remember token not necessary
    }

    public function retrieveByCredentials(array $credentials)
    {
        $user = $this->user;

        foreach ($credentials as $credentialKey => $credentialValue) {
            if (!Str::contains($credentialKey, 'password')) {
                $user->where($credentialKey, $credentialValue);
            }
        }

        return $user->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return app('hash')->check($plain, $user->getAuthPassword());
    }
}
