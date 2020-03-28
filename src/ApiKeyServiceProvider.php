<?php

namespace Kasitaw\ApiKey;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Filesystem\Filesystem;
use Kasitaw\ApiKey\Guards\ApiKeyGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class ApiKeyServiceProvider extends ServiceProvider
{
    public function boot(Filesystem $filesystem)
    {
        $this->publishes([
            __DIR__ . '/../config/api-key.php' => config_path('api-key.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_api_keys_table.php.stub' => $this->getMigrationFileName($filesystem),
        ], 'migrations');

        $this->loadRoutesFrom(__DIR__ . '/../tests/TestRoute/TestRoute.php');

        Auth::extend('api_key', function ($app, $name, array $config) {
            // Automatically build the DI, put it as reference
            $userProvider = app(UserTokenProvider::class);
            $request = app('request');

            return new ApiKeyGuard($userProvider, $request, $config);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/api-key.php',
            'api-key'
        );
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path . '*_create_api_keys_table.php');
            })
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_create_api_keys_table.php")
            ->first();
    }
}
