<?php

namespace App\Models\BLog;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use App\Helpers;
use App\File;

/**
 * Class Author
 * @package App
 */
class Author extends Model
{
    use ModelTranslation;

    /**
     * @var string
     */
    protected $table = 'perevorot_blog_authors';
    protected $backendNamespace = 'Perevorot\Blog\Models\Author';

    public function posts()
    {
        return $this->hasMany('App\Models\Blog\Blog', 'author_id', 'id');
    }

    public function enabled_blogs() {
        return $this->posts()->where('is_enabled','=', 1)->where('type','=', 1);
    }

    public function photo()
    {
        $file = File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
            ->where('field', 'photo')
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