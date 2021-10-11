<?php

namespace JaxWilko\MediaStream\Models;

use Model;
use Winter\Storm\Database\Traits\Validation;

/**
 * User Group Model
 */
class MediaMeta extends Model
{
    use Validation;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'jaxwilko_media_meta';

    protected $rules = [
        'path' => 'required|unique'
    ];

    protected $casts = [
        'data' => 'json'
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'path',
        'data',
    ];
}
