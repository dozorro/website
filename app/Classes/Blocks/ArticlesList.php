<?php

namespace App\Classes\Blocks;

use DB;
use App\Models\Blog\Blog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class ActualTendersAndReviews
 * @package App\Classes\Blocks
 */
class ArticlesList extends IBlock
{
    public $blog;

    /**
     * @return array
     */
    private function getArticles()
    {
        /**
         * @var array $tenders
         */
        //return new Collection([]);
        $articles = $this->blog->getPublishedPosts(['is_main' => 0, 'limit' => $this->block->value->articles_limit, 'type' => Blog::TYPE_BLOG]);

        return $articles;
    }

    /**
     * @return array
     */
    public function get()
    {
        $this->blog = new Blog();

        return [
            'articles' => $this->getArticles(),
            'count' => $this->block->value->articles_limit,
        ];
    }
}
