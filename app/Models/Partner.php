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
class Partner extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_partners';

    public $backendNamespace = 'Perevorot\Dozorro\Models\Partner';

    protected $translations = [
        'text'
    ];

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
}
