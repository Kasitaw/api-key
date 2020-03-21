<?php

namespace Kasitaw\ApiKey;

use Kasitaw\ApiKey\Traits\HasApiKey;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasApiKey;

    public $incrementing = false;
}