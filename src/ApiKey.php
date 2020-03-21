<?php

namespace Kasitaw\ApiKey;

use Kasitaw\ApiKey\Traits\HasApiKey;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasApiKey;

    public $incrementing = false;

    /**
     * Get underlying "model" that ties up to the api key.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function authenticable()
    {
        return $this->morphTo(
            'authenticable',
            'model_type',
            'model_id'
        );
    }

    /**
     * Get active api keys only.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get Inactive api keys only.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeInActive($query)
    {
        return $query->where('status', false);
    }
}
