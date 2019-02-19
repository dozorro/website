<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Helpers;
use App;
use DB;
use Config;

/**
 * Class JsonForm
 * @package App
 */
class JsonForm extends Model
{
    /**
     * @var string
     */
    protected $table = 'perevorot_dozorro_json_forms';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $dates = [
        'date', 'last_date'
    ];

    public $casts = ['is_anon'];

    public static $ngoForms=['F201', 'F202', 'F203', 'F204'];

    /**
     * @var array
     */
    private $comments = [];

    public $userNgo;
    public $userGroup;
    
    public function ngo_profile()
    {
        return $this->belongsTo('App\Models\NgoProfile');
    }

    public function getStatusNameAttribute()
    {
        switch($this->status) {
            case 1:
                return t('ngo.form.status.applied');
                break;
            case 2:
                return t('ngo.form.status.canceled');
                break;
            case 3:
                return t('ngo.form.status.edited');
                break;
            case 0:
            case null:
                return t('ngo.form.status.moderation');
                break;
        }

        return '';
    }
    
    public function checkIsCustomer()
    {
        if($this->schema == 'comment' && $this->user->issetEdrpou($this->entity_id)) {
            $this->is_customer = 1;
            $this->save();
        } elseif($this->schema == 'F203') {
            $f202 = JsonForm::where('object_id', $this->JsonParentForm)
                ->byWithForm(['F202'])
                ->byAnswered(['actionCode-AC010'])
                ->count();

            if($f202) {
                $this->is_customer = 1;
                $this->save();
            }
        }
    }

    public function showGroup() {

        if($this->user && !empty($this->userGroup) && $this->userGroup->slug != 'superadmin' &&
            $this->user->issetEdrpou($this->entity_id)) {
            if(!$this->user->region || $this->userRegion()) {
                return true;
            }
        }

        return false;
    }

    public function userRegion() {

        if(!$this->region) {
            return false;
        }

        foreach(explode(',', $this->user->region) as $region) {
            if (trim($region) == $this->region) {
                return true;
            }
        }

        return false;
    }

    public function isset_comment() {
        return
            (isset($this->json->overallScoreComment) && $this->json->overallScoreComment) ||
            (isset($this->json->abuseComment) && $this->json->abuseComment) ||
            (isset($this->json->actionComment) && $this->json->actionComment) ||
            (isset($this->json->resultComment) && $this->json->resultComment);
    }

    public function showComment()
    {
        if($this->schema == 'F201') {
            $comment = $this->json->overallScoreComment;
        }elseif($this->schema == 'F202') {
            $comment = $this->json->actionComment;
        }elseif($this->schema == 'F203') {
            $comment = $this->json->resultComment;
        }
        elseif($this->schema == 'F204') {
            $comment = $this->json->abuseComment;
        }

        if($this->updated && str_contains($comment, '<br><br>')) {
            $files = explode('<br>', $comment);

            $text = $files[0];
            unset($files[0]);

            $files = $text.'<p>'.t('ngo.form.modal_window.correct_document').'</p>'.join('<br>', $files);

            while($pos = strrpos($files, '<br><br>')) {
                $c = explode('<br><br>', $files);

                if(count($c) > 2) {
                    $files = $pos !== false ? substr_replace($files, '<br>', $pos, strlen('<br><br>')) : $files;
                } else {
                    break;
                }
            }

            $comment = str_replace('{DOWNLOAD_URL}', config('services.localstorage.url'), $files);
        } else {
            $comment = str_replace('{DOWNLOAD_URL}', config('services.localstorage.url'), $comment);
        }
        
        return $comment;
    }
    
    public function link_in_comment() {
        if(isset($this->json->overallScoreComment) && stripos($this->json->overallScoreComment, '</a>') !== FALSE) {
            return [
                'file_name' => $this->cut_file_name($this->json->overallScoreComment),
                'file_link' => $this->cut_file_link($this->json->overallScoreComment),
            ];
        } elseif(isset($this->json->abuseComment) && stripos($this->json->abuseComment, '</a>') !== FALSE) {
            return [
                'file_name' => $this->cut_file_name($this->json->abuseComment),
                'file_link' => $this->cut_file_link($this->json->abuseComment),
            ];
        }elseif(isset($this->json->actionComment) && stripos($this->json->actionComment, '</a>') !== FALSE) {
            return [
                'file_name' => $this->cut_file_name($this->json->actionComment),
                'file_link' => $this->cut_file_link($this->json->actionComment),
            ];
        } elseif(isset($this->json->resultComment) && stripos($this->json->resultComment, '</a>') !== FALSE) {
            return [
                'file_name' => $this->cut_file_name($this->json->resultComment),
                'file_link' => $this->cut_file_link($this->json->resultComment),
            ];
        }

        return false;
    }

    public function cut_file_link($text) {
        return trim(strip_tags($this->cut_str($text, 'href="', '"')));
    }

    public function cut_file_name($text) {
        return trim(strip_tags($this->cut_str($text, '">', '<')));
    }

    public function cut_str($str, $left, $right) {
        $str = substr(@stristr($str, $left), strlen($left));
        $leftLen = strlen(@stristr($str, $right));
        $leftLen = $leftLen ? -($leftLen) : strlen($str);
        $str = substr($str, 0, $leftLen);
        return $str;
    }

    public $__user;

    public function getUserAttribute()
    {
        if(!empty($this->__user))
            return $this->__user;

        if(empty($this->user_id))
            return false;

        return $this->userRelation;
    }

    public function userRelation()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function set_region() {

        $regions = json_decode(file_get_contents(public_path() . '/sources/'.Config::get('locales.current').'/region.json'));

        foreach($regions as $id => $name) {

            if(stripos($id, "-") !== FALSE) {
                $range = explode("-", $id);
            } else {
                $range[] = (int)$id;
                $range[] = (int)$id;
            }

            $pc = substr($this->postal_code, 0, 2);

            for($i = $range[0]; $i <= $range[1]; $i++) {
                if($i == (int)$pc) {
                    $this->region = $id;
                    break 2;
                }
            }
        }
    }

    public function get_region() {

        $regions = json_decode(file_get_contents(public_path() . '/sources/'.Config::get('locales.current').'/region.json'));

        foreach($regions as $id => $name) {

            if(stripos($id, "-") !== FALSE) {
                $range = explode("-", $id);
            } else {
                $range[] = (int)$id;
                $range[] = (int)$id;
            }

            $pc = substr($this->region, 0, 2);

            for($i = $range[0]; $i <= $range[1]; $i++) {
                if($i == (int)$pc) {
                    return $name;
                }
            }
        }

        return '';
    }

    public static function getInfoByCustomer($edrpou)
    {
        $items = self::byEdrpou($edrpou)
            ->byModel('form')
            ->groupBy('tender')
            ->get();

        if (count($items) <= 0) {
            return false;
        }

        $sum = 0;

        foreach ($items as $item) {
            $sum += $item->price;
        }

        $result = self::select(
                DB::raw('COUNT(id) as tenders_reviews')
            )
            ->byModel('form')
            ->byEdrpou($edrpou)
            ->first();

        $result->tenders_sum = $sum;
        $result->tenders_count = count($items);

        return $result;
    }

    public function show_forms2($forms, $tender_id, $form = null) {
        //$data = self::select('id', 'payload')->byTenderId($this->tender)->bySingleForm($form)->get();
        $data = $forms->where('tender', $tender_id)->where('schema', $form);
        $field = "_forms_{$form}";
        $this->$field = '';

        if(!$data->isEmpty()) {

            $_data = [];

            foreach($data AS $v) {
                if (isset($v->json->abuseName)) {
                    $_data[] = $v->json->abuseName;
                } elseif (isset($v->json->generalName)) {
                    $_data[] = $v->json->generalName;
                } elseif (isset($v->json->actionName)) {
                    $_data[] = $v->json->actionName;
                } elseif (isset($v->json->resultName)) {
                    $_data[] = $v->json->resultName;
                }
            }

            $this->$field = !empty($_data) ? '- '.implode('<br>- ', array_unique($_data)) : '';
        }

        return;
    }

    public function show_forms($form = null) {
        $data = self::select('id', 'payload')->byTenderId($this->tender)->bySingleForm($form)->get();

        if(!$data->isEmpty()) {

            $_data = [];

            foreach($data AS $v) {
                if (isset($v->json->abuseName)) {
                    $_data[] = $v->json->abuseName;
                } elseif (isset($v->json->generalName)) {
                    $_data[] = $v->json->generalName;
                } elseif (isset($v->json->actionName)) {
                    $_data[] = $v->json->actionName;
                } elseif (isset($v->json->resultName)) {
                    $_data[] = $v->json->resultName;
                }
            }

            return !empty($_data) ? '- '.implode('<br>- ', array_unique($_data)) : '';
        } else {
            return '';
        }
    }

    public static function getNgoReviews($params = [])
    {
       return self::selectSomeData($params['addSelect'])
        ->byDate($params)
        ->byModel('form')
        ->byEdrpou(@$params['edrpou'])
        ->byRegion(@$params['region'])
        ->byUser(@$params['user'])
        ->byForm(@$params['ngo'], @$params['ngoWithForm'])
        ->byWithForm(@$params['withForm'])
        //->byWithoutForm(@$params['withoutForm'])
        ->byNgoProfile(@$params['ngo_profile'])
        ->exclude(@$params['exclude'])
        ->byCpv(@$params['cpv'])
        ->byStatus(@$params['status'])
        ->byTender(@$params['tender'])
        ->byTender(@$params['tid'])
        ->byFormStatus(@$params['formStatus'])
        ->byGroup(@$params['formStatus'])
        ->byPaginate(@$params['paginate']);
    }

    public function scopeByGroup($query, $data) {
        if($data !== 'moderation') {
            return $query->groupBy('tender');
        }
    }

    public function scopeByDate($query, $data)
    {
        if(!empty($data['date_from'])) {
            $date = Carbon::createFromFormat('d.m.Y', $data['date_from'])->format('Y-m-d H:i:s');
            $query->where('date', '>=', $date);
        }
        if(!empty($data['date_to'])) {
            $date = Carbon::createFromFormat('d.m.Y', $data['date_to'])->format('Y-m-d H:i:s');
            $query->where('date', '<=', $date);
        }

        return $query;
    }

    public function scopebyPaginate($query, $data) {
        if($data) {
            return $query->paginate(10);
        } else {
            return $query->get();
        }
    }

    public function scopeselectSomeData($query, $select) {
        if(isset($select['fields'])) {
            $query->addSelect($select['fields']);
        }
        if(isset($select['*'])) {
            $query->addSelect('*');
        }
        if(isset($select['reviews'])) {
            $query->addSelect(DB::raw('(SELECT COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender) as reviews'));
        }
        if(isset($select['lastDate'])) {
            $query->addSelect(DB::raw("(SELECT forms.date from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender order by date desc limit 0,1) as last_date"));
            $query->orderBy('last_date', 'DESC');
        } else {
            $query->orderBy('date', 'DESC');
        }
        if(isset($select['formType'])) {
            //$query->addSelect(DB::raw('MAX(CAST(SUBSTRING((SELECT forms.schema from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender order by date desc limit 0,1) FROM 2)AS UNSIGNED)) as last_schema'));
            if(in_array($select['formType'], self::$ngoForms)) {
                $query->having(DB::raw("(SELECT forms.schema from perevorot_dozorro_json_forms as forms where forms.schema in ('" . implode("','",
                        self::$ngoForms) . "') and forms.tender = perevorot_dozorro_json_forms.tender order by date desc limit 0,1)"),
                    '=', $select['formType']);
            }
        }
        if(isset($select['payload'])) {
            $query->addSelect(DB::raw("(SELECT forms.payload from perevorot_dozorro_json_forms as forms where forms.schema in ('".implode("','", self::$ngoForms)."') and forms.tender = perevorot_dozorro_json_forms.tender order by date desc limit 0,1) as payload"));
        }
    }

    public static function getReviews($params = [])
    {
        $tenders = self::select(
            '*'/*,
            DB::raw('(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender '.(empty($params['ngo']) ? (" and forms.schema not in ('".implode("','", self::$ngoForms)."')") : '').') as reviews'),
            DB::raw('(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema = "F201") as f201_count'),
            DB::raw("(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema = 'F201' and forms.payload not like '%abuseCode\":\"A028%' and forms.payload not like '%abuseCode\":\"A029%') as f201_count2")
            */)
           /* ->selectRaw('(tender in
                    (
                        SELECT forms.tender from perevorot_dozorro_json_forms as forms where forms.is_customer = 1
                    )) as reaction')*/
            ->byModel('form')
            ->byPriceFrom(@$params['price_from'])
            ->byPriceTo(@$params['price_to'])
            ->byTender(@$params['tender'])
            ->byTender(@$params['tid'])
            ->byEdrpou(@$params['edrpou'])
            ->byCpv(@$params['cpv'])
            ->byStatus(@$params['status'])
            ->byFormType(@$params['type'])
            ->byRegion(@$params['region'])
            ->byReaction(@$params['reaction'])
            ->orderBy('date', 'DESC')
            ->groupBy('tender')
            ->paginate(empty($params['paginate']) ? 10 : $params['paginate']);

        if(!$tenders->isEmpty()) {
            $ids = $tenders->lists('tender', 'id')->toArray();

            $forms = App\JsonForm::select(
                'tender',
                DB::raw('(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender ' . (empty($params['ngo']) ? (" and forms.schema not in ('" . implode("','",
                            App\JsonForm::$ngoForms) . "')") : '') . ') as reviews'),
                DB::raw('(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema = "F201") as f201_count'),
                DB::raw("(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema = 'F201' and forms.payload not like '%abuseCode\":\"A028%' and forms.payload not like '%abuseCode\":\"A029%') as f201_count2")
            )
                ->selectRaw('(tender in
                (
                    SELECT forms.tender from perevorot_dozorro_json_forms as forms where forms.is_customer = 1
                )) as reaction')
                ->whereIn('tender', $ids)->groupBy('tender')->get();

            foreach($tenders as &$tender) {
                foreach($forms as $form) {
                    if($tender->tender == $form->tender) {
                        $tender->reaction = $form->reaction;
                        $tender->reviews = $form->reviews;
                        $tender->f201_count = $form->f201_count;
                        $tender->f201_count2 = $form->f201_count2;
                    }
                }
            }
        }

        return $tenders;
    }

    public function scopeByReaction($query, $data)
    {
        if($data) {
            return
                $query->whereRaw('tender in
                    (
                        SELECT forms.tender from perevorot_dozorro_json_forms as forms where forms.is_customer = 1
                    )');
        }
    }

    public function scopeByAnswered($query, $value)
    {
        foreach($value AS $v) {
            $t = explode('-', $v);
            $query->where('payload', 'like', "%\"{$t[0]}\":\"{$t[1]}\"%");
        }

        return $query;
    }

    public function scopeByFormType($query, $data)
    {
        if ($data === 'tenders') {
            //return $query->whereNotIn('schema', self::$ngoForms);
            //return $query->havingRaw('(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema = "F201") <= 0');
        } elseif($data === 'ngo') {
            return $query->havingRaw('(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema = "F201") > 0');
        }
    }

    public function scopeByWithForm($query, $data)
    {
        if ($data) {
            return $query->where('schema', $data);
        }
    }

    public function scopeByForm($query, $data, $with = false)
    {
        if ($data === false) {
            return $query->whereNotIn('schema', self::$ngoForms);
        } elseif($data === true) {
            return $query->whereIn('schema', ($with ? array_merge(self::$ngoForms, [$with]) : self::$ngoForms));
        }
    }

    public function scopeExclude($query, $data)
    {
        if (sizeof($data) > 0) {
            return $query->whereNotIn('tender', $data);
        }
    }

    public function scopeBySingleForm($query, $data)
    {
        if ($data) {
            return $query->where('schema', $data);
        }
    }

    public function scopeByNgoProfile($query, $data)
    {
        if ($data) {
            return $query->where('ngo_profile_id', $data);
        }
    }

    public function scopeByModel($query, $data)
    {
        if ($data) {
            return $query->where('model', 'form');
        }
    }

    public function scopeByUser($query, $data)
    {
        if (sizeof($data) > 0) {
            return $query->whereIn('user_id', $data);
        }
    }

    public function scopeByRegion($query, $data)
    {
        if (is_array($data) && !empty($data)) {
            return $query->whereIn('region', $data);
        } elseif($data) {
            return $query->whereIn('region', explode(',', $data));
        }
    }

    public function scopeByFormStatus($query, $data)
    {
        if ($data == 'moderation') {
            return $query->whereIn('status', [0,2,3]);
        } else {
            return $query->where('status', 1);
        }
    }

    public function scopeByStatus($query, $data)
    {
        if ($data) {
            return $query->where('tender_status', $data);
        }
    }

    public function scopeByCpv($query, $data)
    {
        if ($data) {
            return $query->where('cpv', 'like', '%' . $data . '%');
        }
    }

    public function scopeByEdrpou($query, $data)
    {
        if ($data && is_array($data) && !empty($data)) {
            return $query->whereIn('entity_id', $data);
        }
    }

    public function scopeByTenderId($query, $data)
    {
        if (!empty($data) && !is_array($data)) {
            return $query->where('tender', $data);
        }elseif (is_array($data)) {
            return $query->whereIn('tender', $data);
        }
    }

    public function scopeByTender($query, $data)
    {
        if (!empty($data) && is_array($data)) {
            return $query->whereIn('tender_id', $data);
        }
        elseif ($data) {
            return $query->where('tender_id', $data);
        }
    }

    public function scopeByPriceTo($query, $data)
    {
        if ($data) {
            return $query->where('price', '<=', $data);
        }
    }

    public function scopeByPriceFrom($query, $data)
    {
        if ($data) {
            return $query->where('price', '>=', $data);
        }
    }

    public function get_tender_json()
    {
        if ($this->tender_json) {
            if ($tender = json_decode($this->tender_json)) {
                return !empty((array)$tender) ? $tender : null;
            }
        }

        return null;
    }

    /**
     * @return object
     */
    public function getJsonAttribute()
    {
        if (is_string(json_decode($this->payload))) {
            $data = json_decode(json_decode($this->payload));
            $this->payload = json_encode($data, JSON_UNESCAPED_UNICODE);
            $this->save();
        }

        $data = json_decode($this->payload);

        if(isset($data->payload->comment)) {
            $data = $data->payload;
        }
        elseif(isset($data->payload->formData)) {
            $data = $data->payload->formData;
        }
        elseif(isset($data->formData)) {
            $data = $data->formData;
        }
        elseif(isset($data->userForm)) {
            $data = $data->userForm;
        } else {
            return null;
        }

        if(isset(json_decode($this->payload)->schema)) {
            $schema = strtr(json_decode($this->payload)->schema, ['tender' => 'F']);
        } else {
            $schema = $this->schema;
        }

        if(App\Classes\Lang::getCurrentLocale() !== App\Classes\Lang::getDefault()) {
            if(in_array($schema, ['F201','F202','F203']) && isset(Settings::instance('perevorot.dozorro.form')->{($schema.'_field')})) {
                $field = Settings::instance('perevorot.dozorro.form')->{($schema . '_field')};
                $code_field = Settings::instance('perevorot.dozorro.form')->{($schema . '_code_field')};

                if(isset($data->{$field}) && isset($data->{$code_field})) {
                    $_data = trim(Settings::instance('perevorot.dozorro.form')->{$schema}) . "\n";
                    $_data = explode("\n", $_data);

                    if ($_data = array_first($_data, function ($k, $v) use ($data, $code_field) {
                        return stripos($v, $data->{$code_field} . '=') !== FALSE;
                    })
                    ) {
                        $data->{$field} = trim(strtr($_data, [($data->{$code_field} . '=') => '']));
                    }
                }
            }
        }

        return $data;
    }

    public function getJsonParentFormAttribute()
    {
        if (is_string(json_decode($this->payload))) {
            $data = json_decode(json_decode($this->payload));
            $this->payload = json_encode($data, JSON_UNESCAPED_UNICODE);
            $this->save();
        }

        $data = json_decode($this->payload);

        if(isset($data->payload->parentForm)) {
            return $data->payload->parentForm;
        }
        elseif(isset($data->parentForm)) {
            return $data->parentForm;
        } else {
            return false;
        }
    }

    /**
     * @return object
     */
    public function getAuthorAttribute()
    {
        $data = json_decode($this->payload);

        return isset($data->payload->author) ? $data->payload->author : $data->author;
    }

    public function showAuthorName() {
        if(isset($this->author->name) && $this->author->name) {
            return $this->author->name;
        }
        elseif(isset($this->author_json->full_name) && $this->author_json->full_name) {
            return $this->author_json->full_name;
        } else {
            return t('tender.review.unknown_user');
        }
    }

    public function getAuthorJsonAttribute()
    {
        if (!empty($this['attributes']['author_json'])) {
            if ($json = json_decode($this['attributes']['author_json'])) {
                return !empty((array)$json) ? $json : null;
            }
        }

        return null;
    }

    private function parsePayload()
    {
        if (!$this->data) {
            $this->data = json_decode($this->data);
        }
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        if(stripos($this->payload, '"{\"') !== FALSE) {
            $payload = trim(stripslashes(json_encode(json_decode($this->payload), JSON_UNESCAPED_UNICODE)), '"');
        } else {
            $payload = $this->payload;
        }
        return json_decode($payload);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return (isset($this->author->auth->id)) ? $this->author->auth->id : null;//md5($this->getPayload()->author->email . $this->getPayload()->author->social);
    }

    public $__comments;

    /**
     * @return Collection
     */
    public function comments()
    {
        if (!is_array($this->__comments))
        {
            $comments = JsonForm::where('schema', 'comment')
                ->where('thread', $this->object_id)
                ->get();

            if(!$comments->isEmpty()) {
                Helpers::parseUserData($comments);
                Helpers::parseUserRelationData($comments);
            }

            $this->__comments = $comments;
        }

        return $this->__comments;
    }

    public function issetF203() {

        $getParentId = "SUBSTRING(payload,LOCATE('parentForm\":\"', payload) + CHAR_LENGTH('parentForm\":\"'), 32)";
        $getParentId2 = "SUBSTRING(forms.payload,LOCATE('parentForm\":\"', forms.payload) + CHAR_LENGTH('parentForm\":\"'), 32)";

        $reviews = self::where('tender', $this->tender)
            ->where('schema', 'F203')
            ->whereRaw($getParentId.' in
                    (
                        SELECT forms.object_id from perevorot_dozorro_json_forms as forms where forms.schema = \'F202\' and forms.payload like \'%actionCode":"AC010%\'
                    )')
            ->first();

        return !empty($reviews);
    }

    public function issetCommentEdrpou() {
        $comments = $this->comments();

        if($comments) {
            foreach ($comments AS $comment) {

                if (!$comment->user_id || !$comment->user) {

                    if(!empty($comment->json->author->identifier->id) &&
                        $comment->json->author->identifier->id == $comment->entity_id) {
                        return true;
                    }

                    continue;
                }

                if ($comment->user->issetEdrpou($comment->entity_id)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function compareEdrpou()
    {

        if(!empty($this->json->author->identifier->id) &&
            $this->json->author->identifier->id == $this->entity_id) {
            return true;
        }

        return false;
    }

    public function checkAuthorStatus()
    {
        if($this->is_anon) {
            return 'not-confirmed';
        } elseif(
            $this->getPayload()->owner == env('JSONFORMS_OWNER_ID')
        ) {
            return 'other-confirmed';
        } elseif($this->getPayload()->owner != env('JSONFORMS_OWNER_ID')) {
            return 'confirmed';
        } else {
            return 'not-confirmed';
        }
    }

    public static function getF112Enum($key)
    {
        $data = json_decode(file_get_contents(public_path() . '/sources/forms/F112.json'), true);

        return array_get($data, 'properties.formData.form.2.items.0.titleMap_uk.' . $key);
    }

    public static function getF110Enum($key)
    {
        $data = json_decode(file_get_contents(public_path() . '/sources/forms/F110.json'), true);

        return array_get($data, 'properties.formData.form.4.items.0.titleMap_uk.' . $key);
    }

    public static function getCommentsCount($params = [])
    {
        return JsonForm::
            where('model', '=', 'comment')
            ->byTenderIds(@$params['ids'])
            ->count();
    }

    public static function getReviewsCount($params = [])
    {
        return JsonForm::
            where('model', '!=', 'comment')
            ->byTenderIds(@$params['ids'])
            ->byForm(@$params['ngo'])
            ->count();
    }

    public function scopebyTenderIds($query, $data)
    {
        if($data)
        {
            return $query->whereIn('tender', $data);
        }
    }

    public function scopebyParent($query, $data)
    {
        if($data) {
            return $query->where('payload', 'like', '%parentForm":"'.$data.'%');
        } else {
            return $query->where('payload', 'not like', '%parentForm%');
        }
    }
}
