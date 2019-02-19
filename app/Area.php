<?php

namespace App;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 * @package App
 */
class Area extends Model
{
    use ModelTranslation;

    /**
     * @var string
     */
    protected $table = 'perevorot_dozorro_areas';

    /**
     * @var string
     */
    protected $backendNamespace = 'Perevorot\Dozorro\Models\Area';

    protected $translations = [
        'title',
        'description',
    ];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeIsEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * @return mixed
     */
    public function image()
    {
        return File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
            ->where('field', 'image')
            ->orderBy('id', 'DESC')
            ->first()
        ;
    }
}
