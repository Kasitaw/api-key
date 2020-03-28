<?php

namespace Kasitaw\ApiKey\Tests\TestModel;

use Kasitaw\ApiKey\Traits\HasApiKey;

class User extends \Illuminate\Foundation\Auth\User
{
    use HasApiKey;
}
