<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Page;

class Menu extends Model
{
    /**
     * @var string
     */
    protected $table = 'perevorot_page_menu';

    /**
     * @var bool
     */
    public $timestamps = true;

    public $fillable = [
        'alias',
        'title',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
