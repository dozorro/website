<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\File;
use App\Helpers;

class Translate extends Model
{
    /**
     * @var string
     */
    protected $table = 'rainlab_translate_attributes';

    /**
     * Convert to native type
     *
     * @var array
     */
    protected $casts = ['is_enabled', 'is_default', 'is_current'];

    public function scopeIsEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

}