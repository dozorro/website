<?php

namespace App;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use ModelTranslation;

    public $table = 'perevorot_dozorro_customers';
    public $backendNamespace = 'Perevorot\Dozorro\Models\Customer';
    public $timestamps=false;

    public function forms()
    {
        return $this->hasMany('App\JsonForm', 'entity_id', 'main_edrpou');
    }

    public static function findByEdrpou($data) {
        if($r = self::where('main_edrpou', $data)->first()) {
            $r->translate();
        }

        return $r;
    }

    public function all_forms() {

        $edrpos[] = $this->main_edrpou;

        if($this->edrpou) {
            $tpm = explode("\n", str_replace("\r", '', trim($this->edrpou)));
            $edrpos = array_merge($edrpos, $tpm);
        }

        return JsonForm::byEdrpou($edrpos)->get();
    }

    public function tenders_count()
    {
        $forms = $this->all_forms();

        if(!$forms->isEmpty()) {
            return count(array_unique($this->all_forms()->lists('tender')->toArray()));
        } else {
            return 0;
        }
    }

    public function tenders_sum()
    {
        $forms = $this->all_forms();

        if(!$forms->isEmpty()) {
            foreach ($forms->groupBy('tender') AS $form) {
                $prices[] = (float)$form[0]->price;
            }

            return array_sum($prices);
        } else {
            return 0;
        }
    }

    public function image()
    {
        $file = File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
            ->where('field', 'image')
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
