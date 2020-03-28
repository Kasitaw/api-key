<?php

namespace Kasitaw\ApiKey\Tests;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return ['Kasitaw\ApiKey\ApiKeyServiceProvider'];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('auth.guards.api_key', [
            'driver' => 'api_key',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('api_keys', function (Blueprint $table) {
            $table->string('uuid');
            $table->morphs('model');
            $table->text('key');
            $table->boolean('status');
            $table->timestamp('last_access_at')->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
        });
    }
}
