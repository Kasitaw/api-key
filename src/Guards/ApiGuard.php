<?php

namespace Kasitaw\ApiKey\Guards;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\UserProvider;

class ApiGuard
{
    use GuardHelpers;

    /**
     * Key that will check in request.
     *
     * @var mixed|string
     */
    private $inputKey;

    /**
     * Key that will be check in database table.
     *
     * @var mixed|string
     */
    private $storageKey;

    /**
     * Request object.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    public function __construct(UserProvider $provider, Request $request, $configuration)
    {
        $this->provider = $provider;
        $this->request = $request;

        // Key to check in request
        $this->inputKey = config('api-key.request_key.api_key') ?? 'api_key';

        // Key to check in database
        $this->storageKey = config('api-key.columns.key') ?? 'key';
    }

    /**
     * Get the user based on token.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $apiKey = null;

        if ($token = $this->getTokenForRequest()) {
            // Retrieve user by provided token
            $apiKey = $this->provider->retrieveByToken(
                $this->storageKey,
                $token
            );
        }
        if ($apiKey) {
            $this->setUser($apiKey->authenticable);

            $apiKey->last_access_at = Carbon::now();
            $apiKey->save();

            return $apiKey->authenticable;
        }

        return null;
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        return $token;
    }

    /**
     * Validate a user's credentials.
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        $credentials = [$this->storageKey => $credentials[$this->inputKey]];

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }
}
