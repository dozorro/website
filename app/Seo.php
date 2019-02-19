<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    /**
     * @var string
     */
    protected $table = 'perevorot_seo_seo';

    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $baseUrl
     * @return Settings|mixed
     */
    public function scopeFindByUrlMask($query, $baseUrl)
    {
        $urls = self::getUrlMasks($baseUrl);

        $query->where('url_mask', $baseUrl);

        foreach ($urls as $url) {
            $query->orWhere('url_mask', htmlspecialchars($url));
        }

        return $query;
    }

    /**
     * @param $baseUrl
     * @return Settings
     */
    public static function getUrls($baseUrl)
    {
        $items = [];
        $seo = (new static())
            ->findByUrlMask($baseUrl)
            ->get()
//            ->toArray()
        ;

        foreach ($seo as $item) {
            $items[substr_count($item->url_mask, '*')] = $item;
        }

        if (sizeof($items) > 0) {
            $minKey = min(array_keys($items));

            return $items[$minKey];
        }

        $settings = Settings::instance('perevorot_seo_settings');

        return $settings;
    }

    /**
     * @param $url
     * @return array
     */
    public static function getUrlMasks($url)
    {
        $urls = [];
        $i = sizeof(explode('/', $url));

        while ($i > 0) {
            $url = explode('/', $url);
            $url[$i - 1] = '*';
            $url = implode('/', $url);

            $urls[] = $url;

            $i--;
        }

        return $urls;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
