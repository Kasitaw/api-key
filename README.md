
API Key Guard for Laravel
===================

This package makes it easy to authenticate users using user defined `api key` authentication guard with Laravel 6.0+

## Installation

API Key can be installed via composer:

```
composer require "kasitaw/api-key"
```

The package will automatically register itself.

You can publish the migration with:

```
php artisan vendor:publish --provider="Kasitaw\ApiKey\ApiKeyServiceProvider" --tag=migrations
```

After the migration has been published, run the migrations with following command:

```
php artisan migrate
```

You can publish the config file with:

```
php artisan vendor:publish --provider="Kasitaw\ApiKey\ApiKeyServiceProvider" --tag=config
```

This is the contents of the published config file:

```php
<?php

return [
    /**
     * Model use to configure Api Key
     */
    'model' => [
        'api_key' => Kasitaw\ApiKey\ApiKey::class, // Make sure use Kasitaw\ApiKey\Traits\HasApiKey.php trait if you use your own modal
    ],

    /**
     * Table name that reflected to the above model.
     */
    'table_name' => [
        'api_keys' => 'api_keys', // Table name to the above model
    ],

    /**
     * Column name being used to store generated api key
     */
    'columns' => [
        'key' => 'key',
    ],

    /**
     * Field name that being used to fetch the "apiKey". Either passed through query params or as a body.
     */
    'request_key' => [
        'api_key' => 'api_key',
    ],

    /**
     * Generated key length.
     */
    'key_length' => 80,
];
```

## Usages
Before started, configure `config/auth.php` guard as following:

```php
'guards' => [
    'web' => [
        //
    ],

    'api' => [
        //
    ],

    /*
     * Adding new `api_key` key into guards section 
     */
    'api_key' => [
        'driver' => 'api_key',
    ]
],
```

Use `HasApiKey.php` trait inside `App\User.php` model or any model that implement `\Illuminate\Contracts\Auth\Authenticatable` interface:

```php
<?php

namespace App;

use Kasitaw\ApiKey\Traits\HasApiKey;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiKey;
}
```

And finally, call it in middleware as following:

```php
// Using `auth:api` as regular user authentication
Route::get('/users', function() {
    // 
})->middleware('auth:api');

// Using `auth:api_key` to authenticate user for external api
Route::get('/external/intergation/users', function() {
    dd(request()->user());
    // or using Auth::guard('api_key')->user()
    // or using auth('api_key')->user()
})->middleware('auth:api_key');
```

## Available Methods to manage the `key`

 **Generate new api** key that ties up to the authenticate user
 ```php
 $user->generateNewKey(); // By default will activate the key, pass `false` params to make it inactive
```

 **Activate** all existing keys
```php
$user->activateAllKeys();
```

 **Activate** the key using `key`
```php
$user->activateKeyByKey('J1VFYTgUafp21ljEkanJYYnlY1j4REURXgAKzlwAUxABfCWPw4PBw9HKYbG4GWNvi125WUO0P2e7MmqC');

// or 

$user->activateKeyByKey(
    'J1VFYTgUafp21ljEkanJYYnlY1j4REURXgAKzlwAUxABfCWPw4PBw9HKYbG4GWNvi125WUO0P2e7MmqC',
    '5c9fuEbAny4737an7hXC9VdNmDzd1yE0qn6Am9R8nNzJ0HWROn1daMJ19Lp36XLJlI5QIAkv6xYUkt6U'
);
```

**Activate** the key using `uuid`
```php
$user->activateKeyByUuid('e0b9ed50-31b4-4ed6-a0f7-71490fa15ad6');

// or

$user->activateKeyByUuid(
    'e0b9ed50-31b4-4ed6-a0f7-71490fa15ad6',
    '597a67f8-9c19-4c2b-98ff-8020c0f7e360'
);
```

**Revoke** all existing keys
```php
$user->revokeAllKeys();
```

**Revoke** the key using `key`
```php
$user->revokeKeyByKey('J1VFYTgUafp21ljEkanJYYnlY1j4REURXgAKzlwAUxABfCWPw4PBw9HKYbG4GWNvi125WUO0P2e7MmqC');

// or 

$user->revokeKeyByKey(
    'J1VFYTgUafp21ljEkanJYYnlY1j4REURXgAKzlwAUxABfCWPw4PBw9HKYbG4GWNvi125WUO0P2e7MmqC',
    '5c9fuEbAny4737an7hXC9VdNmDzd1yE0qn6Am9R8nNzJ0HWROn1daMJ19Lp36XLJlI5QIAkv6xYUkt6U'
);
```

**Revoked** the key using `uuid`
```php
$user->revokeKeyByUuid('e0b9ed50-31b4-4ed6-a0f7-71490fa15ad6');

// or

$user->revokeKeyByUuid(
    'e0b9ed50-31b4-4ed6-a0f7-71490fa15ad6',
    '597a67f8-9c19-4c2b-98ff-8020c0f7e360'
);
```

**Delete** the key using `key`
```php
$user->removeKeyByKey('J1VFYTgUafp21ljEkanJYYnlY1j4REURXgAKzlwAUxABfCWPw4PBw9HKYbG4GWNvi125WUO0P2e7MmqC');

// or 

$user->removeKeyByKey(
    'J1VFYTgUafp21ljEkanJYYnlY1j4REURXgAKzlwAUxABfCWPw4PBw9HKYbG4GWNvi125WUO0P2e7MmqC',
    '5c9fuEbAny4737an7hXC9VdNmDzd1yE0qn6Am9R8nNzJ0HWROn1daMJ19Lp36XLJlI5QIAkv6xYUkt6U'
);
```

**Delete** the key using `uuid`
```php
$user->removeKeyByUuid('e0b9ed50-31b4-4ed6-a0f7-71490fa15ad6');

// or

$user->removeKeyByUuid(
    'e0b9ed50-31b4-4ed6-a0f7-71490fa15ad6',
    '597a67f8-9c19-4c2b-98ff-8020c0f7e360'
);
```

Get all keys
```php
$keys = $user->api_keys;

foreach($keys as $key) {
    // 
}
```

**Delete** all keys
```php
$user->removeAllKeys();
```

Get all **active** keys
```php
$keys = $user->api_keys()->active()->get();

foreach($keys as $key) {
    // 
}
```

Get all **in-active** keys
```php
$keys = $user->api_keys()->inActive()->get();

foreach($keys as $key) {
    // 
}
```

**Check** whether key is active
```php
$key = $user->api_keys->first();

dd($key->isActive());
```

Or directly check the key is active
```php
$uuid = 'e0b9ed50-31b4-4ed6-a0f7-71490fa15ad6';
$user->isKeyActive($uuid); // true/false, return null if key not found

// or
$key = 'J1VFYTgUafp21ljEkanJYYnlY1j4REURXgAKzlwAUxABfCWPw4PBw9HKYbG4GWNvi125WUO0P2e7MmqC';
$user->isKeyActive($key);
```



