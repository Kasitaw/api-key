<?php

return [
    /*
     * Model use to configure Api Key
     */
    'model' => [
        'api_key' => Kasitaw\ApiKey\ApiKey::class, // Make sure use Kasitaw\ApiKey\Traits\HasApiKey.php trait if you use your own modal
    ],

    /*
     * Table name that reflected to the above model.
     */
    'table_name' => [
        'api_keys' => 'api_keys', // Table name to the above model
    ],

    /*
     * Column name being used to store generated api key
     */
    'columns' => [
        'key' => 'key',
    ],

    /*
     * Field name that being used to fetch the "apiKey". Either passed through query params or as a body.
     */
    'request_key' => [
        'api_key' => 'api_key',
    ],

    /*
     * Generated key length.
     */
    'key_length' => 80,
];
