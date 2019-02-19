<?php

namespace App\Classes\Blocks;

use DB;
use App\Models\Blog\Blog;
use Config;
use Illuminate\Support\Collection;

/**
 * Class ActualTendersAndReviews
 * @package App\Classes\Blocks
 */
class LatestArticles extends IBlock
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
        $articles = $this->blog->getPublishedPosts(['is_main' => 0, 'limit' => $this->block->value->articles_limit, 'type' => Blog::TYPE_NEWS]);

        if($articles instanceof Blog) {
            $articles = new Collection([$articles]);
        }

        return $articles;
    }

    private function getMainArticle()
    {
        /**
         * @var array $tenders
         */
        $article = $this->blog->getPublishedPosts(['limit' => 1, 'is_main' => true]);

        return (isset($article->id) ? $article : false);
    }

    /**
     * @return array
     */
    public function get()
    {
        $this->blog = new Blog();

        return [
            'articles' => $this->getArticles(),
            'main_article' => $this->getMainArticle(),
        ];
    }
}
