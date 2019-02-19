<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use App\File;
use App\Helpers;

/**
 * Class Blog
 * @package App
 */
class Complaint extends Model
{
    use ModelTranslation;

    CONST TYPE_BEFORE = 1;
    CONST TYPE_EXTRA = 2;

    /**
     * @var string
     */
    protected $table = 'perevorot_dozorro_complaints';

    /**
     * Convert to native type
     *
     * @var array
     */
    protected $casts = ['is_enabled'];

    public $backendNamespace = 'Perevorot\Dozorro\Models\Complaint';

    public function scopeIsEnabled($query)
    {
        return $query->where($this->table . '.is_enabled', true)->order('sort_order', 'ASC');
    }

    public function scopebyType($query, $data)
    {
        if($data)
        {
            return $query->where($this->table . '.type', $data);
        }
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