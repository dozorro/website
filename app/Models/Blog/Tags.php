<?php

namespace App\Models\Blog;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tags
 * @package App
 */
class Tags extends Model
{
    use ModelTranslation;

    /**
     * @var string
     */
    protected $table = 'perevorot_blog_tags';
    protected $backendNamespace = 'Perevorot\Blog\Models\Tag';
}