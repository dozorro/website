<?php

namespace App\Traits;

use App\Classes\Lang;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait ModelTranslation
{
    private $_defaultLocale = null;
    private $_currentLocale = null;

    public function translate($backendNamespace = null)
    {
        $this->_currentLocale = $this->_currentLocale ? $this->_currentLocale : Lang::getCurrentLocale();
        $this->_defaultLocale = $this->_defaultLocale ? $this->_defaultLocale : Lang::getDefault();

        if($this->_currentLocale !== $this->_defaultLocale) {
            $this->attributes = array_replace($this->attributes,
                (array)json_decode(
                    $this->belongsTo('App\Models\Translate', 'id', 'model_id')
                        ->where('model_type', $backendNamespace ? $backendNamespace : $this->backendNamespace)
                        ->where('locale', $this->_currentLocale)
                        ->value('attribute_data')
                ));
        }

        return $this;
    }

    /**
     *
     */
    public function getTranslations()
    {
        $model = DB::table('rainlab_translate_attributes')
            ->where('locale', App::getLocale())
            ->where('model_id', $this->id)
            ->where('model_type', $this->backendNamespace)
            ->first()
        ;

        if(!$this->is_backend && $this->type != 3 && $this->type != 4 && !$this->{'longread_'.Lang::getCurrentLocale()}) {
            $this->title = null;
        }

        if (!$model) {
            return;
        }

        $attributes = json_decode($model->attribute_data);

        foreach ($attributes as $field => $value) {
            $this->{$field} = $value;
        }

        if(!$this->is_backend && $this->type != 4 && !$this->{'longread_'.Lang::getCurrentLocale()}) {
            $this->title = null;
        }
    }

    /**
     *
     */
    public function insertTranslations()
    {
        if (App::getLocale() == Lang::getDefault()) {
            return;
        }

        $fields = [];

        foreach ($this->translations as $field) {
            $fields[$field] = $this->{$field};
        }

        $attributes = [
            'locale' => App::getLocale(),
            'model_id' => $this->id,
            'model_type' => $this->backendNamespace,
            'attribute_data' => json_encode($fields),
        ];

        DB::table('rainlab_translate_attributes')
            ->insert($attributes)
        ;
    }
}
