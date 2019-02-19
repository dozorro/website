<?php

namespace App\Models\Blog;

use App\Classes\Lang;
use App\Traits\ModelTranslation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\File;
use App\Helpers;

/**
 * Class Blog
 * @package App
 */
class Blog extends Model
{
    use ModelTranslation;

    CONST TYPE_BLOG = 1;
    CONST TYPE_NEWS = 2;

    /**
     * @var string
     */
    protected $table = 'perevorot_blog_posts';

    /**
     * Convert to native type
     *
     * @var array
     */
    protected $casts = ['is_enabled', 'is_main'];
    protected $dates = ['published_at', 'created_at'];
    public $backendNamespace = 'Perevorot\Blog\Models\Blog';

    public function author()
    {
        return $this->belongsTo('App\Models\Blog\Author');
    }

    public function getAuthor() {
        $author = $this->author ? $this->author->translate() : null;

        if($author && !$author->attributes['full_name']) {
            $author->attributes['full_name'] = $author->getOriginal('full_name');
        }

        return $author;
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Blog\Tags', 'perevorot_blog_tag_to_post', 'post_id', 'tag_id');
    }

    public function getTags() {
        return $this->tags->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });
    }

    public function scopeIsEnabled($query)
    {
        return $query->where($this->table . '.is_enabled', true);
    }

    public function scopebyType($query, $data)
    {
        if($data)
        {
            return $query->where($this->table . '.type', $data);
        }
    }

    public function scopeByQ($query, $data)
    {
        if(!empty($data))
        {
            return $query->where(function($query) use($data) {
                $query->where($this->table.'.title', 'like', '%'.$data.'%')
                    ->orwhere($this->table.'.short_description', 'like', '%'.$data.'%')
                    ->orwhere($this->table.'.longread_ua', 'like', '%'.$data.'%');
            });
        }
    }

    public function scopeByDate($query, $data)
    {
        if(!empty($data['date_from'])) {
            $date = Carbon::createFromFormat('d.m.Y', $data['date_from'])->format('Y-m-d H:i:s');
            $query->where($this->table.'.created_at', '>=', $date);
        }
        if(!empty($data['date_to'])) {
            $date = Carbon::createFromFormat('d.m.Y', $data['date_to'])->format('Y-m-d H:i:s');
            $query->where($this->table.'.created_at', '<=', $date);
        }

        return $query;
    }

    public function scopeIsMain($query, $data)
    {
        if($data !== null)
        {
            return $query->where($this->table . '.is_main', $data);
        }
    }

    public function scopeByTag($query, $data)
    {
        if($data !== null)
        {
            return $query
                ->join('perevorot_blog_tag_to_post', 'perevorot_blog_posts.id', '=', 'perevorot_blog_tag_to_post.post_id')
                ->join('perevorot_blog_tags', 'perevorot_blog_tag_to_post.tag_id', '=', 'perevorot_blog_tags.id')
                ->where('perevorot_blog_tags.slug', $data);
        }
    }

    public function scopeByNgo($query, $data)
    {
        if($data !== null)
        {
            return $query
                ->join('perevorot_blog_authors', 'perevorot_blog_posts.author_id', '=', 'perevorot_blog_authors.id')
                ->join('perevorot_dozorro_ngo_profiles', 'perevorot_blog_authors.ngo_profile_id', '=', 'perevorot_dozorro_ngo_profiles.id')
                ->where('perevorot_dozorro_ngo_profiles.slug', $data);
        }
    }

    public function scopeByAuthor($query, $data)
    {
        if($data !== null)
        {
            return $query
                ->join('perevorot_blog_authors', 'perevorot_blog_posts.author_id', '=', 'perevorot_blog_authors.id')
                ->where('perevorot_blog_authors.slug', $data);
        }
    }

    public function scopebyLimit($query, $limit)
    {
        if($limit > 1)
        {
            $data = $query->orderBy('created_at', 'desc')->paginate($limit);

            foreach($data AS $k => $item) {
                $data[$k] = $item->translate();
            }

            return $data;
        }
        elseif($limit == 1)
        {
            $row = $query->first();
            return ($row ? $row->translate() : null);
        }
        elseif($limit == 'count')
        {
            return $query->count();
        }
        elseif($limit == 'all')
        {
            return $query->orderBy('created_at', 'desc')->get()->each(function ($item, $key) {
                return $item ? $item->translate() : null;
            });
        }
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

    public function getPublishedPosts($params = [])
    {
        return self::select($this->table.'.*')
            ->with(['tags', 'author'])
            ->ByLongread(Lang::getCurrentLocale())
            ->isEnabled()
            ->byRegion(@$params['region'])
            ->isMain(@$params['is_main'])
            ->byQ(@$params['q'])
            ->byDate($params)
            ->byTag(@$params['tag'])
            ->byType(@$params['type'])
            ->byAuthor(@$params['author'])
            ->byNgo(@$params['ngo'])
            ->byLimit(@$params['limit']);
    }

    public function scopeByRegion($query, $data)
    {
        if($data) {
            return $query->where($this->table.'.region', 'like', '%'.$data.'%');
        }
    }

    public function scopeByLongread($query, $locale) {
        return $query
            ->whereNotNull('longread_'.$locale)
            ->where('longread_'.$locale, '!=', '');
    }

    public function findBySLug($slug)
    {
        $r = self::select($this->table.'.*')
            ->ByLongread(Lang::getCurrentLocale())
            ->where('slug', $slug)
            ->first();

        return $r ? $r->translate() : null;
    }

    public function clear_title()
    {
        return htmlentities(strip_tags($this->title), ENT_QUOTES);
    }

    public function clear_short_description($value)
    {
        return str_limit(trim(htmlentities(strip_tags($value?$value:$this->short_description), ENT_QUOTES)), 300);
    }
}