<?php

namespace App\Http\Controllers;

use App\Models\Blog\Blog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App;
use App\ActualTender;
use Config;

class BlogController extends BaseController
{
    public $blog;
    private $request;
    private $regions;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->blog = new Blog();

        $FormController = app('App\Http\Controllers\FormController');

        foreach ($FormController->get_region_data() as $one)
            $this->regions[$one['id']] = $one['name'];

        setlocale(LC_ALL, "en_US.UTF-8");
        asort($this->regions, SORT_LOCALE_STRING);
    }

    public function byNgo($slug = null)
    {
        return $this->blog(null, null, $slug);
    }

    public function byAuthor($slug = null)
    {
        return $this->blog(null, $slug);
    }

    public function byTag($slug = null)
    {
        return $this->blog($slug);
    }

    public function news($tag = null, $author = null)
    {
        if($this->request->ajax()) {
            $views = '';
            $news = $this->blog->getPublishedPosts([
                'limit' => 10,
                'tag' => $tag,
                'author' => $author,
                'is_main' => false,
                'type' => Blog::TYPE_NEWS,
                'q' => $this->request->has('q') ? $this->request->get('q') : null,
                'region' => $this->request->has('region') ? $this->request->get('region') : null,
                'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
                'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
            ]);

            $groups = $news->groupBy(function($item) {
                return $item->created_at->format('d.m.Y');
            });

            foreach($groups as $date => $news) {
                if($this->request->get('date') != $date) {
                    $views .= view('partials/news/ajax_post', [
                        'date' => $date,
                    ])->render();
                }
                foreach ($news AS $item) {
                    $views .= view('partials/news/post', [
                        'post' => $item,
                    ])->render();
                }
            }

            return response()->json(['data' => $views]);
        }

        $news = $this->blog->getPublishedPosts([
            'limit' => 10,
            'tag' => $tag,
            'author' => $author,
            'is_main' => false,
            'type' => Blog::TYPE_NEWS,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);

        $groups = $news->groupBy(function($item) {
            return $item->created_at->format('d.m.Y');
        });

        $main = $this->blog->getPublishedPosts([
            'limit' => 1,
            'is_main' => true,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $latest_posts = $this->blog->getPublishedPosts([
            'limit' => 3,
            'type' => Blog::TYPE_NEWS,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $tenders = ActualTender::getAllActualTenders(['limit' => 3]);

        return $this->render('pages/news/index', [
            'news' => $news,
            'groups' => $groups,
            'main' => (isset($main->id) ? $main : false),
            'tenders' => $tenders,
            'latest_posts' => $latest_posts,
            'regions' => $this->regions,
        ]);
    }

    public function blog($tag = null, $author = null, $ngo = null)
    {
        if($this->request->ajax()) {
            $views = '';
            $articles = $this->blog->getPublishedPosts([
                'is_main' => 0,
                'limit' => 10,
                'type' => Blog::TYPE_BLOG,
                'ngo' => $ngo,
                'tag' => $tag,
                'author' => $author,
                'region' => $this->request->has('region') ? $this->request->get('region') : null,
                'q' => $this->request->has('q') ? $this->request->get('q') : null,
                'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
                'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
            ]);

            foreach($articles AS $item) {
                $views .= view('partials/blog/post', [
                    'post' => $item,
                ])->render();
            }

            return response()->json(['data' => $views]);
        }

        $posts = $this->blog->getPublishedPosts([
            'limit' => 10,
            'ngo' => $ngo,
            'tag' => $tag,
            'author' => $author,
            'is_main' => false,
            'type' => Blog::TYPE_BLOG,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $main = $this->blog->getPublishedPosts([
            'limit' => 1,
            'is_main' => true,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $latest_posts = $this->blog->getPublishedPosts([
            'limit' => 3,
            'type' => Blog::TYPE_NEWS,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $tenders = ActualTender::getAllActualTenders(['limit' => 3]);

        return $this->render('pages/blog/index', [
            'posts' => $posts,
            'main' => (isset($main->id) ? $main : false),
            'tenders' => $tenders,
            'latest_posts' => $latest_posts,
            'regions' => $this->regions,
        ]);
    }

    public function ajax_blog_longread()
    {
        if($this->request->ajax()) {
            $views = '';
            $articles = $this->blog->getPublishedPosts([
                'is_main' => 0,
                'limit' => $this->request->get('count'),
                'type' => Blog::TYPE_BLOG,
                'region' => $this->request->has('region') ? $this->request->get('region') : null,
                'q' => $this->request->has('q') ? $this->request->get('q') : null,
                'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
                'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
            ]);

            foreach($articles AS $item) {
                $views .= view('partials/blog/post2', [
                    'article' => $item,
                ])->render();
            }

            return response()->json(['data' => $views]);
        }

        return response()->json(['data' => '']);
    }

    public function blog_page($slug)
    {
        if(!$slug)
        {
            return redirect()->route('page.blog');
        }

        $post = $this->blog->findBySlug($slug);

        if(!$post)
        {
            abort(404);
            return $this->render('errors/404', []);
        }

        $locale = App\Classes\Lang::getCurrentLocale();
        $blocks = (array) json_decode($post->{'longread_' . $locale});
        $blocks = new App\Classes\Longread($blocks, $post->id, $this->blog->backendNamespace);
        $blocks = $blocks->getBlocks();

        $latest_posts = $this->blog->getPublishedPosts([
            'limit' => 3,
            'type' => Blog::TYPE_NEWS,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $banner = $this->blog->getPublishedPosts([
            'limit' => 1,
            'is_main' => true,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $tenders = ActualTender::getAllActualTenders(['limit' => 3]);

        $image = $post->photo();

        if(empty($image)) {

            /*
            $values = array_map(function($item) {
                return implode(' ', array_filter(array_values((array)$item)));
            }, array_column($blocks, 'value'));

            $body = implode(' ', array_values($values));dd($body);
            $image = trim(App\Helpers::cut_str(App\Helpers::cut_str($body, '<img', '>'), 'src="', '"'));*/

            $block = array_first($blocks, function($key, $item) {
                return !empty($item->files);
            });

            if(!empty($block)) {
                $image = array_first($block->value, function ($key, $item) {
                    return $item instanceof App\File;
                });

                if (!empty($image)) {
                    $image = App\Helpers::getStoragePath($image->disk_name);
                }
            }
        }

        $this->seoData['og_title'] = $this->seoData['title'] = $post->title;
        $this->seoData['og_description'] = $this->seoData['description'] = strip_tags($post->short_description);
        $this->seoData['og_image'] = $image;
        $this->seoData['keywords'] = implode(', ', array_column($post->getTags()->toArray(), 'name'));

        return $this->render('pages/blog/show', [
            'post' => $post,
            'banner' => (isset($banner->id) ? $banner : false),
            'latest_posts' => $latest_posts,
            'blocks' => $blocks,
            'tenders' => $tenders,
        ]);
    }

    public function news_page($slug)
    {
        if(!$slug)
        {
            return redirect()->route('page.news');
        }

        $post = $this->blog->findBySlug($slug);

        if(!$post)
        {
            abort(404);
            return $this->render('errors/404', []);
        }

        $locale = App\Classes\Lang::getCurrentLocale();
        $blocks = (array) json_decode($post->{'longread_' . $locale});
        $blocks = new App\Classes\Longread($blocks, $post->id, $this->blog->backendNamespace);

        $latest_posts = $this->blog->getPublishedPosts([
            'limit' => 3,
            'type' => Blog::TYPE_NEWS,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $banner = $this->blog->getPublishedPosts([
            'limit' => 1,
            'is_main' => true,
            'region' => $this->request->has('region') ? $this->request->get('region') : null,
            'q' => $this->request->has('q') ? $this->request->get('q') : null,
            'date_from' => $this->request->has('date_from') ? $this->request->get('date_from') : null,
            'date_to' => $this->request->has('date_to') ? $this->request->get('date_to') : null,
        ]);
        $tenders = ActualTender::getAllActualTenders(['limit' => 3]);

        $image = $post->photo();

        if(empty($image)) {

            $block = array_first($blocks, function($key, $item) {
                return !empty($item->files);
            });

            if(!empty($block)) {
                $image = array_first($block->value, function ($key, $item) {
                    return $item instanceof App\File;
                });

                if (!empty($image)) {
                    $image = App\Helpers::getStoragePath($image->disk_name);
                }
            }
        }

        $this->seoData['og_title'] = $this->seoData['title'] = $post->title;
        $this->seoData['og_description'] = $this->seoData['description'] = strip_tags($post->short_description);
        $this->seoData['og_image'] = $image;
        $this->seoData['keywords'] = implode(', ', array_column($post->getTags()->toArray(), 'name'));

        return $this->render('pages/news/show', [
            'post' => (isset($post->id) ? $post : false),
            'banner' => (isset($banner->id) ? $banner : false),
            'latest_posts' => $latest_posts,
            'blocks' => $blocks->getBlocks(),
            'tenders' => $tenders,
        ]);
    }
}
