<?php

namespace App\Models;

use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\File;

/**
 * Model
 */
class JudgePractice extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_judge_practices';

    public $backendNamespace = 'Perevorot\Dozorro\Models\JudgePractice';

    protected $translations = [
        'name'
    ];

    public function items()
    {
        return $this->hasMany('App\Models\JudgePracticeItem');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->orderBy('sort_order', 'asc');
    }

    public function image()
    {
        $file = File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
            ->where('field', 'image')
            ->orderBy('id', 'DESC')
            ->first();

        if($file)
        {
            return $file = Helpers::getStoragePath($file->disk_name);
        }
        else
        {
            return '';
        }
    }

    public function findBySLug($slug)
    {
        return self::select($this->table.'.*')
            ->where('slug', $slug)
            ->first()->translate();
    }
}
