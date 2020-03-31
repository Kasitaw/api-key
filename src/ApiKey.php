<?php

namespace Kasitaw\ApiKey;

use Illuminate\Support\Str;
use Kasitaw\ApiKey\Traits\HasApiKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiKey extends Model
{
    use HasApiKey;
    use SoftDeletes;

    public $incrementing = false;

    protected $guarded = [];

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    protected $dates = [
        'last_access_at',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        /**
         * Generate uuid.
         */
        static::creating(function ($model) {
            $model->incrementing = false;
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        });
    }

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

    /**
     * Check whether key is active or inactive;.
     *
     * @return bool
     */
    public function isActive()
    {
        return true === $this->status;
    }

    /**
     * Activate the current key.
     *
     * @return $this
     */
    public function activate()
    {
        $this->update([
            'status' => true,
        ]);

        return $this;
    }

    /**
     * Revoke the current key.
     *
     * @return $this
     */
    public function revoke()
    {
        $this->update([
            'status' => false,
        ]);

        return $this;
    }
}
