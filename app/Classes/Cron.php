<?php

namespace App\Classes;

use App\File;
use App\Models\Badge;
use App\Models\BadTenderLog;
use App\Models\BLog\Author;
use App\Models\Blog\Blog;
use App\Models\Coin;
use App\Models\Pair;
use App\Models\SyncLog;
use App\Settings;
use DB;
use App\Helpers;
use App\JsonForm;
use Carbon\Carbon;
use Config;
use App\Classes\Api;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Classes\json_forms_old;
use App\Customer;
use App\Models\NgoProfile;

class Cron
{
    private static $json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;
    private static $setting;

    private static $F2X = [
        'F201',
        'F202',
        'F203',
        'F204',
    ];

    public static function customerCount()
    {
        $customer=new Customer();
        $ngo=new NgoProfile();

        DB::table($customer->table)->update([
            'count_forms'=>0,
            'count_comments'=>0
        ]);

        DB::table($ngo->table)->update([
            'count_forms'=>0
        ]);

        $customers = Customer::where('is_enabled', 1)->get();
        
        foreach($customers as $customer) {
            if($customer->edrpou) {
                $edrpous = array_filter(array_map(function($ev) {
                    return trim($ev);
                }, explode("\n", $customer->edrpou)), function($ev) {
                        return $ev;
                });

                $customer->count_comments = JsonForm::
                    where('model', 'comment')
                    ->whereIN('entity_id', $edrpous)
                    ->whereRaw('YEAR(date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)')
                    ->count();

                $customer->count_forms = JsonForm::
                    where('model', 'form')
                        ->whereIN('entity_id', $edrpous)
                        ->whereRaw('YEAR(date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)')
                        ->count();

                $customer->save();
            }
        }

        $ngos=NgoProfile::where('is_enabled', 1)->get();

        foreach($ngos as $ngo) {
            $ngo->count_forms=JsonForm::where('ngo_profile_id', '=', $ngo->id)->count();
            $ngo->save();
        }
    }

    public static function resetRisks()
    {
        DB::table('dozorro_model_answers')->update([
            'answer'=>null,
            'time_answered'=>null,
            'time_shown'=>null,
        ]);
    }

    public static function setIsCustomer()
    {
        $i = 0;

        $reviews = JsonForm:://select('*')
            //->selectRaw('SUBSTRING(payload,LOCATE(\'parentForm":"\', payload) + CHAR_LENGTH(\'parentForm":"\'), 32) as parentForm')
            byWithForm(['F203'])
            ->get();

        foreach($reviews as $review) {

            $f202 = JsonForm::where('object_id', $review->JsonParentForm)
                ->byWithForm(['F202'])
                ->byAnswered(['actionCode-AC010'])
                ->count();

            if($f202) {
                $review->is_customer = 1;
                $review->save();

                $i++;
            }
        }

        $comments = JsonForm::select('*')
            ->byWithForm(['comment'])
            ->get();

        if($comments) {
            foreach ($comments AS $comment) {
                if (!$comment->user_id || !$comment->user) {
                    if(!empty($comment->json->author->identifier->id) &&
                        $comment->json->author->identifier->id == $comment->entity_id) {
                        $comment->is_customer = 1;
                        $comment->save();
                        $i++;
                    }
                }
                elseif ($comment->user->issetEdrpou($comment->entity_id)) {
                    $comment->is_customer = 1;
                    $comment->save();
                    $i++;
                }
            }
        }

        return $i;
    }

    public static function fixForms() {

        $JsonFormController = app('App\Http\Controllers\JsonFormController');
        $forms = JsonForm::select('id', 'object_id', 'payload', 'thread')
            ->where('payload', 'like', '%supplierQuestionsComment%')
            ->where('payload', 'not like', '%communicationMethod%')
            ->where('payload', 'not like', '%answeredInTime%')
            ->where('payload', 'not like', '%procuringQuestions%')
            ->where('payload', 'not like', 'supplierQuestions%')
            ->get();

        foreach($forms as $form) {
            $payload = $form->getPayload();

            $data = new \stdClass();
            $data->communicationMethod = "phone";
            $data->supplierQuestionsComment = $payload->payload->formData->supplierQuestionsComment;
            $payload->payload->formData = $data;

            $form->payload = json_encode($payload, $JsonFormController->json_options);
            $form->object_id = $JsonFormController->hash_id($form->payload);
            $form->save();
        }

        return $forms->count();
    }

    public static function reSaveOwners($old_id = null, $new_id = null) {

        $owner = env('JSONFORMS_OWNER_ID');

        if(!$old_id) {
            JsonForm::where('is_sync', false)->update([
                'owner' => $owner
            ]);
        }

        $JsonFormController = app('App\Http\Controllers\JsonFormController');
        $forms = JsonForm::select('id', 'object_id', 'payload', 'thread')->byParent($old_id)->get();

        $total=sizeof($forms);

        if(!$old_id) {
            echo "Total parents " . $total . ' (' . Carbon::now() . ')' . "\n";
            usleep(5);
        }

        $cnt=0;

        foreach ($forms AS $form) {
            $cnt++;
            $payload = $form->getPayload();
            $payload->owner = $owner;
            $old_id = $form->object_id;

            if($new_id && isset($payload->payload->parentForm)) {
                $payload->payload->parentForm = $new_id;
                $form->thread = $form->thread ? $new_id : $form->thread;
            }

            $form->payload = json_encode($payload, $JsonFormController->json_options);
            $form->object_id = $JsonFormController->hash_id($form->payload);
            $form->save();

            usleep(5);
            echo $cnt.'/'.$total.PHP_EOL;

            if($c = JsonForm::select('id')->byParent($old_id)->count()) {
                echo "Parent found " . $c . ' (' . Carbon::now() . ')' . "\n";
                self::reSaveOwners($old_id, $form->object_id);
            }
        }

        return;
    }

    public static function reSaveAuthors($old_id = null, $new_id = null) {
        $JsonFormController = app('App\Http\Controllers\JsonFormController');
        $forms = JsonForm::select('id', 'object_id', 'payload', 'thread')->byParent($old_id)->get();

        if(!$old_id) {
            echo "Total parents " . sizeof($forms) . ' (' . Carbon::now() . ')' . "\n";
            usleep(5);
        }

        foreach ($forms AS $form) {
            $payload = $form->getPayload();

            if(!isset($payload->payload->author->auth->id)) {
                continue;
            }

            if($form->user && !$form->is_anon) {
                $id = hash_hmac('sha256', $form->user->social_id, env('API_FORMS_USER_KEY', 'not_anon'));
            } elseif($form->user && $form->is_anon) {
                $id = hash_hmac('sha256', $form->user->social_id, env('API_FORMS_ANON_KEY', 'anon'));
            } else {
                $id = hash_hmac('sha256', ($form->user_id ? $form->user_id : $form->date), env('API_FORMS_ANON_KEY', 'anon'));
            }

            $payload->payload->author->auth->id = substr($id, 0, 32);
            $old_id = $form->object_id;

            if($new_id && isset($payload->payload->parentForm)) {
                $payload->payload->parentForm = $new_id;
                $form->thread = $form->thread ? $new_id : $form->thread;
            }

            $form->payload = json_encode($payload, $JsonFormController->json_options);
            $form->object_id = $JsonFormController->hash_id($form->payload);
            $form->save();

            usleep(5);
            echo '.';

            if($c = JsonForm::select('id')->byParent($old_id)->count()) {
                echo "Parent found " . $c . ' (' . Carbon::now() . ')' . "\n";
                self::reSaveAuthors($old_id, $form->object_id);
            }
        }

        return;
    }

    public static function reSaveForms($old_id = null, $new_id = null) {
        $JsonFormController = app('App\Http\Controllers\JsonFormController');
        $forms = JsonForm::select('id', 'object_id', 'payload', 'thread')->byParent($old_id)->get();

        if(!$old_id) {
            echo "Total parents " . sizeof($forms) . ' (' . Carbon::now() . ')' . "\n";
            usleep(5);
        }

        foreach ($forms AS $form) {
            $payload = $form->getPayload();

            if(isset($payload->schema)) {

                $payload->model = "form/" . $payload->schema;
                unset($payload->schema);

                if(!empty($payload->payload->parentForm) && $new_id) {
                    $payload->payload->parentForm = $new_id;
                    $form->thread = $form->thread ? $new_id : $form->thread;
                }

                $old_id = $form->object_id;

                $form->payload = json_encode($payload, $JsonFormController->json_options);
                $form->object_id = $JsonFormController->hash_id($form->payload);
                $form->save();

                usleep(5);
                echo '.';

                if($c = JsonForm::select('id')->byParent($old_id)->count()) {
                    echo "Parent found " . $c . ' (' . Carbon::now() . ')' . "\n";
                    self::reSaveForms($old_id, $form->object_id);
                }
            }
        }

        return;
    }

    public static function addCoins()
    {
        $forms = JsonForm::where('schema', 'F201')
            ->whereNotNull('ngo_profile_id')
            ->where('is_hide', 0)
            ->get();
        $count = 0;
        $setting = Settings::instance('perevorot.dozorro.form');

        if(!$forms->isEmpty()) {
            foreach($forms as $_k => $form) {
                if($form->ngo_profile) {

                    // coins for f201
                    if(isset($form->json->abuseCode) && $prices = @$setting->F201_prices) {
                        $code = $form->json->abuseCode;
                        $item = array_first($prices, function($k, $v) use($code) {
                            return $v['code'] == $code;
                        });

                        if($item && $price = @$item[($form->schema . "_price")]) {
                            $comment = @$setting->{$form->schema.'_formName'} . ' : ' . $form->json->abuseCode.'='.$form->json->abuseName . ' : '.$form->tender_id . ' : ' . $form->object_id;
                            $coin = new Coin([
                                'object_id' => $form->object_id,
                                'sum' => $price,
                                'type' => 1,
                                'dt' => Carbon::now(),
                                'author' => 'Dozorro',
                                'comment' => $comment,
                            ]);
                            $form->ngo_profile->coin()->save($coin);
                            $count += $price;
                        }

                        // coins for f201 children
                        $_forms = JsonForm::where('payload', 'like', "%{$form->object_id}%")
                            ->whereNotNull('user_id')
                            ->where('is_hide', 0)
                            ->get();

                        if($item && !$_forms->isEmpty()) {
                            foreach($_forms as $_form) {
                                if($_form->ngo_profile && $price = @$item[($_form->schema . "_price")]) {
                                    if(!isset($_form->json->reason)) {
                                        $data = (array)$_form->json;
                                        $data = array_filter($data, function($v, $k) {
                                            return stripos($k, 'Comment') === FALSE;
                                        }, ARRAY_FILTER_USE_BOTH);
                                        $_data = implode('=', $data);
                                    } else {
                                        $_data = 'reason='.$_form->json->reason;
                                    }

                                    $comment = @$setting->{$_form->schema.'_formName'} . ' : ' . $_data . ' : '.$_form->tender_id . ' : ' . $_form->object_id;

                                    $coin = new Coin([
                                        'object_id' => $_form->object_id,
                                        'sum' => $price,
                                        'type' => 1,
                                        'dt' => Carbon::now(),
                                        'author' => 'Dozorro',
                                        'comment' => $comment,
                                    ]);
                                    $_form->ngo_profile->coin()->save($coin);
                                    $count += $price;
                                }
                            }
                        }
                    }

                    // add coins for F202 children
                    if(!$_k) {
                        $_forms = JsonForm::whereIn('schema', ['F203', 'F204'])
                            ->where('payload', 'not like', "%{$form->object_id}%")
                            ->whereNotNull('user_id')
                            ->where('is_hide', 0)
                            ->get();

                        foreach($_forms as $_form) {
                            $__form = JsonForm::where('object_id', $_form->jsonParentForm)->where('is_hide', 0)->first();

                            if(!isset($_form->json->reason)) {
                                $data = (array)$_form->json;
                                $data = array_filter($data, function($v, $k) {
                                    return stripos($k, 'Comment') === FALSE;
                                }, ARRAY_FILTER_USE_BOTH);
                                $_data = implode('=', $data);
                            } else {
                                $_data = 'reason='.$_form->json->reason;
                            }

                            $comment = @$setting->{$_form->schema.'_formName'} . ' : ' . $_data . ' : '.$_form->tender_id . ' : ' . $_form->object_id;

                            if($__form && $__form->schema == 'F202') {
                                $__form = JsonForm::where('object_id', $__form->jsonParentForm)->where('is_hide', 0)->first();

                                if($__form->ngo_profile && isset($__form->json->abuseCode) && $prices = @$setting->F201_prices) {
                                    $code = $__form->json->abuseCode;
                                    $item = array_first($prices, function ($k, $v) use ($code) {
                                        return $v['code'] == $code;
                                    });

                                    if ($price = @$item[($__form->schema . "_price")]) {
                                        $coin = new Coin([
                                            'object_id' => $_form->object_id,
                                            'sum' => $price,
                                            'type' => 1,
                                            'dt' => Carbon::now(),
                                            'author' => 'Dozorro',
                                            'comment' => $comment,
                                        ]);
                                        $__form->ngo_profile->coin()->save($coin);
                                        $count += $price;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $count;
    }

    public static function badgesAssign()
    {
        file_put_contents(storage_path('app/badges.lock'), null);

        $ngos = new NgoProfile();
        $ngos = $ngos->byEnabled()->get();
        self::$setting = Settings::instance('perevorot.dozorro.form');
        $schemas = [
            'F201',
            'F202',
            'F203',
            'F204',
        ];

        try {
            foreach ($ngos as $ngo) {

                $data = [];
                $_badges = $ngo->badges();
                $badges = $_badges->where('is_auto', 0)->lists('id');
                $auto = $_badges->lists('id')->toArray();

                if(!empty($badges)) {
                    foreach ($badges AS $id) {
                        $data[$id] = ['is_auto' => 0];
                    }
                }

                foreach ($schemas AS $k) {

                    $badge = false;

                    $_ngo = $ngo->with(['forms' => function($query) use($k) {
                        $query->BySingleForm($k);

                        if($k == 'F204') {
                            $query->where('payload', 'like', '%"reason":"succes"%');
                        }
                    }])->where('id', $ngo->id)->first();

                    if($coins = $_ngo->forms->count()) {
                        foreach (range(1, 3) AS $n) {
                            if ((int)@self::$setting->{"{$k}_badge{$n}"} &&
                                (int)@self::$setting->{"{$k}_price{$n}"} &&
                                (int)@self::$setting->{"{$k}_price{$n}"} <= $coins
                            ) {
                                $badge = @self::$setting->{"{$k}_badge{$n}"};
                                break;
                            }
                        }
                    }

                    if($badge) {
                        $data[$badge] = ['is_auto' => 1, 'dt' => Carbon::now(), 'author' => 'Dozorro'];
                    }
                }

                if($ngo->badges()->sync($data)) {
                    foreach($data as $badge => $v) {
                        if($v['is_auto'] && !in_array($badge, $auto)) {

                            if(!$_badge = Badge::find($badge)) {
                                continue;
                            }

                            $blog = new Blog();
                            $blog->is_badge = 1;
                            $blog->type = 2;
                            $blog->title = $ngo->title.' | '.$_badge->name;
                            $blog->author_id = Author::first()->id;
                            $blog->save();

                            $image = $_badge->get_image(); unset($image->id);
                            $image->attachment_type = 'Perevorot\Blog\Models\Blog';
                            $image->attachment_id = $blog->id;
                            $image->field = 'photo';
                            $file = new File($image->toArray());
                            $file->save();
                        }
                    }
                }
            }
        } catch(\Exception $e) {
            echo 'error ' . $e->getMessage();
            @unlink(storage_path('app/badges.lock'));
        }

        @unlink(storage_path('app/badges.lock'));
    }

    public static function reSaveComments()
    {
        /*
        if(!is_writable(storage_path('tenders/'))) {
            echo 'error writable'; exit;
        }
        */

        $uniq_tenders = JsonForm::where('model', 'comment')->where('payload', 'not like', '%parentForm%')->orderBy('id')->groupBy('tender')->get()->count();

        if(!$uniq_tenders) {
            return ['before' => 0, 'after' => 0];
        }

        $JsonFormController = app('App\Http\Controllers\JsonFormController');
        $api = new Api();
        $comments = JsonForm::where('model', 'comment')->where('payload', 'not like', '%parentForm%')->orderBy('id')->get();

        foreach ($comments AS $comment) {
            $_forms = JsonForm::where('model', 'form')
                ->where('schema', 'not like', '%F20%')
                ->where('tender', $comment->tender)
                ->orderBy('id')
                ->get();

            if($_forms->count() == 1) {

                // resave if have one form
                $comment->thread = $_forms[0]->object_id;
                $comment->payload = self::reformatPayload($comment, $_forms[0]->object_id);
                $comment->object_id = $JsonFormController->hash_id($comment->payload);
                $comment->save();
            } else {

                // resave from old forms
                $_forms = JsonForm::where('model', 'form')
                    ->where('schema', 'not like', '%F20%')
                    ->where('tender', $comment->tender)
                    ->orderBy('id')
                    ->get();

                $old_forms = array_where(json_forms_old::$json_forms_old, function($k, $v) use($comment) {
                    return $v['tender'] == $comment->tender && stripos($v['schema'], 'F20') === FALSE && $v['model'] == 'form';
                });

                $old_comments = array_where(json_forms_old::$json_forms_old, function($k, $v) use($comment) {
                    return $v['tender'] == $comment->tender && $v['schema'] == 'comment' && $v['model'] == 'comment';
                });

                foreach($_forms as $_form) {

                    // search old form by id
                    $old_form = array_first($old_forms, function($k, $v) use($_form) {
                        return $v['id'] == $_form->id && $v['schema'] == $_form->schema && $v['schema'] == $_form->schema;
                    });

                    if($old_form) {

                        // search old comments by old form
                        $_old_comments = array_where($old_comments, function($k, $v) use($old_form) {
                            return $old_form['object_id'] == $v['thread'];
                        });

                        if(empty($_old_comments)) continue;

                        $ids = [];

                        foreach($_old_comments AS $_v) {
                            $ids[] = $_v['id'];
                        }

                        // serach commetns
                        $_comments = JsonForm::where('model', 'comment')
                            ->where('schema', 'comment')
                            ->where('tender', $comment->tender)
                            ->whereIn('id', $ids)
                            ->where('payload', 'not like', '%parentForm%')
                            ->get();

                        if($_comments->isEmpty()) continue;

                        // resave comment
                        foreach($_comments AS $_comment) {
                            $_comment->thread = $_form->object_id;
                            $_comment->payload = self::reformatPayload($_comment, $_form->object_id);
                            $_comment->object_id = $JsonFormController->hash_id($_comment->payload);
                            $_comment->save();
                        }
                    }
                }
            }
        }

        $_after_uniq_tenders = JsonForm::where('model', 'comment')->where('payload', 'not like', '%parentForm%')->orderBy('id')->groupBy('tender')->get()->count();

        return ['before' => $uniq_tenders, 'after' => $_after_uniq_tenders];
        exit;

        $comments = JsonForm::where('model', 'comment')->where('payload', 'not like', '%parentForm%')->groupBy('tender')->orderBy('id')->get();

        dd($comments[0]);

        while(true) {

            foreach ($comments AS $form) {

                if (file_exists(storage_path('tenders/') . $form->tender_id . '.html')) {
                    continue;
                }

                $url = "https://www.google.com.ua/search?q=site:dozorro.org/tender/{$form->tender_id}&oq=site:dozorro.org/tender/{$form->tender_id}";

                try {

                    if ($r = $api->okGoogle($url)) {
                        if (stripos($r, 'id="ires"') !== FALSE) {
                            $res = substr($r, strpos($r, 'id="ires"'), 300);
                            if ($res = Helpers::cut_str($res, 'href="/url?q=', '&amp')) {
                                if (stripos($res, '/tender/') !== FALSE) {
                                    if ($res = $api->okGoogle($res)) {
                                        file_put_contents(storage_path('tenders/') . $form->tender_id . '.html', $res);
                                    }
                                }
                            }
                        }
                    }

                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }

    public static function syncForms()
    {
        file_put_contents(storage_path('app/syncForms.lock'), null);

        try {
            $api = new Api();
            $view_log = new Logger('Log update');
            $view_log->pushHandler(new StreamHandler(storage_path('logs/sync_forms_' . Carbon::now()->format('Y-m-d') . '.log'),
                Logger::NOTICE));

            $PageController = app('App\Http\Controllers\PageController');
            $JsonFormController = app('App\Http\Controllers\JsonFormController');

            $PageController->search_type = 'tender';

            $count = 0;
            $tender_error = 0;
            $api_error = 0;
            $tenders = [];

            $syncLog = new SyncLog();
            $syncLog->created_at = Carbon::now();
            $syncLog->save();

            $data = $api->getForms();

            if (empty($data)) {
                $syncLog->api_errors = 1;
                $syncLog->updated_at = Carbon::now();
                $syncLog->save();
                return;
            }

            $next_page = $data->next_page->offset;
            $offsets = [];
            $wait = false;

            $view_log->addNotice("Starting on $next_page...");

            while (true) {

                if(isset($data->data) && sizeof($data->data) > 0) {
                    foreach ($data->data as $item) {
                        $notIsset = !JsonForm::where('object_id', $item->id)->first();

                        if ($notIsset) {
                            //dump('New form: '.$item->id);
                            //usleep(5);

                            sleep(1);
                            $api_form = $api->getForm($item->id);

                            if (empty($api_form->envelope)) {
                                $api_error++;
                                continue;
                            }

                            $admin = $api_form->envelope->model == 'admin' || $api_form->envelope->owner == 'root';
                            $_data = null;

                            if (!$admin) {
                                if (isset($tenders[$api_form->envelope->payload->tender])) {
                                    $_data = $tenders[$api_form->envelope->payload->tender];
                                } else {
                                    sleep(1);
                                    $json = self::getSearchResults($api_form->envelope->payload->tender, __FUNCTION__);
                                    $_data = json_decode($json);
                                    $_data = @$_data->data;
                                }

                                if (!empty($_data->id) && !isset($tenders[$_data->id])) {
                                    $tenders[$_data->id] = $_data;
                                }

                                if (empty($_data) && !BadTenderLog::where('object_id', $item->id)->first()) {
                                    $badTenderLog = new BadTenderLog();
                                    $badTenderLog->tender_id = $api_form->envelope->payload->tender;
                                    $badTenderLog->object_id = $item->id;
                                    $badTenderLog->created_at = Carbon::now();
                                    $badTenderLog->save();

                                    $tender_error++;
                                    $view_log->addNotice("Review error: no tender", [
                                        'object_id' => $api_form->id,
                                        'tender_id' => $api_form->envelope->payload->tender
                                    ]);

                                    usleep(100);
                                    continue;
                                }
                            }

                            if ($admin || !empty($_data)) {
                                $form = new JsonForm();

                                $form_model = $api_form->envelope->model == 'form/comment' ? 'comment' : 'form';
                                $form_schema = $api_form->envelope->model == 'form/comment' ? 'comment' : str_replace('form/tender',
                                    'F', $api_form->envelope->model);

                                $form->model = $form_model;
                                $form->schema = $form_schema;
                                $form->owner = $api_form->envelope->owner;
                                $form->user_id = null;
                                $form->payload = json_encode($api_form->envelope, $JsonFormController->json_options);
                                $form->date = Carbon::createFromTimestamp(strtotime($api_form->envelope->date))->format('Y-m-d H:i:s');
                                $form->is_sync = 1;
                                $form->is_hide = 1;
                                $form->was_sent = 1;
                                $form->object_id = $api_form->id;

                                if (!$admin) {
                                    $tender = $_data;

                                    $form->is_hide = 0;
                                    $form->is_anon = empty($api_form->envelope->payload->author->name);
                                    $form->tender = $api_form->envelope->payload->tender;
                                    $form->thread = isset($api_form->envelope->payload->parentForm) && $api_form->envelope->model == 'form/comment' ? $api_form->envelope->payload->parentForm : null;
                                    $form->entity_id = !empty($tender->procuringEntity->identifier->id) ? $tender->procuringEntity->identifier->id : null;
                                    $form->tender_json = $JsonFormController->getTenderJson($tender);
                                    $form->author_json = isset($api_form->envelope->payload->author) ? json_encode($api_form->envelope->payload->author,
                                        $JsonFormController->json_options) : null;
                                    $form->price = $tender->value->amount;
                                    $form->tender_status = $tender->status;
                                    $form->tender_id = $tender->tenderID;

                                    if (isset($tender->procuringEntity->address->postalCode) && is_numeric($tender->procuringEntity->address->postalCode)) {
                                        $form->postal_code = $tender->procuringEntity->address->postalCode;
                                        $form->set_region();
                                    }

                                    if (isset($tender->items) && !empty($tender->items)) {

                                        $cpvs = [];

                                        foreach ($tender->items AS $item) {
                                            if (!in_array($item->classification->id, $cpvs)) {
                                                $cpvs[] = $item->classification->id;
                                            }
                                        }

                                        if (!empty($cpvs)) {
                                            $form->cpv = implode(',', $cpvs);
                                        }
                                    }

                                    if (!empty($api_form->envelope->payload->author->identifier->id) &&
                                        $api_form->envelope->payload->author->identifier->id == $form->entity_id
                                    ) {
                                        $form->is_customer = 1;
                                    }
                                }

                                $form->save();
                                $count++;

                                $view_log->addNotice("New review", ['object_id' => $api_form->id]);
                                //usleep(100);
                            } else {
                                $tender_error++;
                                $view_log->addNotice("Review error: no tender",
                                    [
                                        'object_id' => $api_form->id,
                                        'tender_id' => $api_form->envelope->payload->tender
                                    ]);
                                //usleep(100);
                            }
                        } else {
                            $view_log->addNotice("Form exist");
                            //usleep(5);
                        }
                    }
                }

                if($wait) {
                    $view_log->addNotice("Waiting new forms on $next_page...");
                    sleep(30);
                }

                $view_log->addNotice("Continue with $next_page...");
                sleep(1);
                $data = $api->getForms('?offset=' . $next_page);

                if(isset($data->next_page->offset) && !in_array($data->next_page->offset, $offsets)) {
                    if(!isset($data->data) || sizeof($data->data) <= 0) {
                        $view_log->addNotice("Empty on $next_page...");
                        array_pop($offsets);
                        $next_page = end($offsets);
                        $wait = true;
                    } else {
                        $next_page = $data->next_page->offset;
                        $offsets[] = $data->next_page->offset;
                        $wait = false;
                    }
                }
                else {
                    $view_log->addNotice("Empty $next_page...");
                    array_pop($offsets);
                    $next_page = end($offsets);
                    $wait = true;
                }
            }

            $syncLog->updated_at = Carbon::now();
            $syncLog->api_errors = $api_error;
            $syncLog->forms_added = $count;
            $syncLog->tender_not_found = $tender_error;
            $syncLog->save();
        } catch(\Exception $e) {
            $view_log->addNotice('Error ' . $e->getMessage());
            @unlink(storage_path('app/syncForms.lock'));
        }

        @unlink(storage_path('app/syncForms.lock'));

        return;
    }

    public static function sendForms()
    {
        file_put_contents(storage_path('app/sendForms.lock'), null);

        $api = new Api();
        $count = 0;

        try {
            $F1X = (bool)env('API_FORMS_SEND', false);
            $F2X = (bool)env('API_FORMS_SEND_F2X', false);

            if (!$F1X && !$F2X) {
                return 0;
            }

            $forms = JsonForm::where('was_sent', 0)
                ->where('is_hide', 0)
                ->where('is_sync', 0)
                ->orderBy('date', 'asc')
                ->get();

            foreach ($forms AS $form) {

                if (in_array($form->schema, self::$F2X)) {
                    $send = $F2X;
                } else {
                    $send = $F1X;
                }

                if ($form->schema == 'comment' && !empty($form->thread)) {
                    $_form = JsonForm::where('object_id', $form->thread)->first();

                    if (in_array($_form->schema, self::$F2X)) {
                        $send = (bool)env('API_FORMS_SEND_F2X', false);
                    } else {
                        $send = (bool)env('API_FORMS_SEND', false);
                    }
                }

                if (!$send) {
                    continue;
                }

                $response = $api->sendForm($form, 'cron-send');

                if ($response) {
                    $form->was_sent = 1;
                    $form->save();
                    $count++;
                }

                //usleep(100000);
                sleep(1);
            }
        } catch(\Exception $e) {
            echo 'error ' . $e->getMessage();
            @unlink(storage_path('app/sendForms.lock'));
        }

        @unlink(storage_path('app/sendForms.lock'));

        return $count;
    }

    public static function commentsLocalUpdate($badForms = null)
    {
        $forms = JsonForm::whereIn('object_id', $badForms)->get();
        $total = sizeof($forms);
        $count = 0;

        echo "Total " . $total . ' (' . Carbon::now() . ')' . "\n";
        usleep(5);

        if (!$forms->isEmpty()) {
            foreach ($forms as $form) {
                try {
                    $_form = $form->toArray();

                    if (is_object($_form['author_json'])) {
                        $_form['author_json'] = json_encode($_form['author_json']);
                    }
                    if (is_object($_form['tender_json'])) {
                        $_form['tender_json'] = json_encode($_form['tender_json']);
                    }
                    if (is_object($_form['payload'])) {
                        $_form['payload'] = json_encode($_form['payload']);
                    }

                    DB::table('perevorot_dozorro_json_forms_suspended')->insert($_form);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    exit;
                }

                $form->delete();
                $count++;
            }
        }

        return $count;
        exit;

        $forms = JsonForm::
            where('payload', 'like', '%"owner":"dozorro.org"%')
            ->where('schema', 'NOT LIKE', 'F2%')
            ->where('model', '!=', 'admin')
            ->get();

        $count = 0;
        $JsonFormController = app('App\Http\Controllers\JsonFormController');

        if (!$forms->isEmpty()) {
            $total = sizeof($forms);

            echo "Total " . $total . ' (' . Carbon::now() . ')' . "\n";
            usleep(5);

            foreach ($forms as $form) {
                $form->payload = self::reformatPayload($form);
                $new_object_id = $JsonFormController->hash_id($form->payload);
                $form->object_id = $new_object_id;

                if($form->schema == 'comment') {
                    $form->model = 'comment';
                }

                $form->save();
                $count++;
            }
        }

        if($badForms) {
            $forms = JsonForm::whereIn('object_id', $badForms)->get();
            $total = sizeof($forms);

            echo "Total " . $total . ' (' . Carbon::now() . ')' . "\n";
            usleep(5);

            foreach($forms as $form) {
                if($form->object_id == '4b4a5d24e180e4fc5b3bdb58ec705e5b') {
                    $form->payload = self::reformatPayload($form);
                    $new_object_id = $JsonFormController->hash_id($form->payload);
                    $form->object_id = $new_object_id;

                    if($form->schema == 'comment') {
                        $form->model = 'comment';
                    }

                    $form->save();
                    $count++;
                } else {
                    try {
                        $_form = $form->toArray();

                        if(is_object($_form['author_json'])) {
                            $_form['author_json'] = json_encode($_form['author_json']);
                        }
                        if(is_object($_form['tender_json'])) {
                            $_form['tender_json'] = json_encode($_form['tender_json']);
                        }
                        if(is_object($_form['payload'])) {
                            $_form['payload'] = json_encode($_form['payload']);
                        }

                        DB::table('perevorot_dozorro_json_forms_suspended')->insert($_form);
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                        exit;
                    }

                    $form->delete();
                    $count++;
                }
            }
        }

        return $count;
    }

    public static function formsLocalUpdate()
    {
        JsonForm::where('is_sync', false)->update([
            'owner'=>env('JSONFORMS_OWNER_ID')
        ]);

        $forms = JsonForm::where('payload', 'not like', '%parentForm%')
            ->where('is_hide', 0)
            ->where('is_sync', 0)
            ->where('schema', 'NOT LIKE', 'F2%')
            ->orderBy('date', 'desc')
            ->get();

        $count = 0;

        if(!$forms->isEmpty()) {
            $total = sizeof($forms);

            echo "Total ".$total.' ('.Carbon::now().')'."\n";
            usleep(5);

            $JsonFormController = app('App\Http\Controllers\JsonFormController');

            foreach($forms as $form) {

                    $form->is_anon = !$form->author_json ? 1 : $form->is_anon;
                    $form->payload = self::reformatPayload($form);
                    $new_object_id = $JsonFormController->hash_id($form->payload);
                    $old_object_id = $form->object_id;
                    $form->object_id = $new_object_id;
                    $form->save();

                    $count++;

                    echo '.';
                    usleep(5);

                    $_forms = JsonForm::where('payload', 'like', '%'.$old_object_id.'%')
                        ->where('is_hide', 0)
                        ->where('is_sync', 0)
                        ->where('schema', 'NOT LIKE', 'F2%')
                        ->get();

                    if(!$_forms->isEmpty()) {
                        foreach ($_forms as $_form) {

                            $_form->is_anon = !$_form->author_json ? 1 : $_form->is_anon;
                            $_form->payload = self::reformatPayload($_form, $new_object_id);
                            $_new_object_id = $JsonFormController->hash_id($_form->payload);
                            $old_object_id = $_form->object_id;

                            if ($_form->model == 'comment') {
                                $_form->thread = $new_object_id;
                            }

                            $_form->object_id = $_new_object_id;
                            $_form->save();

                            $count++;

                            echo '.';
                            usleep(5);

                            $__forms = JsonForm::where('payload', 'like', '%' . $old_object_id . '%')
                                ->where('is_hide', 0)
                                ->where('is_sync', 0)
                                ->where('schema', 'NOT LIKE', 'F2%')
                                ->get();

                            if (!$__forms->isEmpty()) {
                                echo "Parent found ".sizeof($__forms)."\n";
                                usleep(5);

                                foreach ($__forms as $__form) {

                                    $__form->is_anon = !$__form->author_json ? 1 : $__form->is_anon;
                                    $__form->payload = self::reformatPayload($__form, $_new_object_id);
                                    $__new_object_id = $JsonFormController->hash_id($__form->payload);

                                    if ($__form->model == 'comment') {
                                        $__form->thread = $_new_object_id;
                                    }

                                    $__form->object_id = $__new_object_id;
                                    $__form->save();

                                    $count++;

                                    echo '.';
                                    usleep(5);
                                }
                            }
                        }
                    }
            }
        }

        return $count.' ('.Carbon::now().')';
    }

    private static function reformatPayload($form, $parent = null)
    {
        $JsonFormController = app('App\Http\Controllers\JsonFormController');
        $user = $form->user;

        $payload = [
            'owner' => $form->owner,
            'model' => $form->model,
            'schema' => str_replace('F', 'tender', $form->schema),
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $form->date)->toAtomString(),
            'payload' => [
                'tender' => $form->tender,
            ]
        ];

        $_payload=json_decode($form->payload);

        if($user && !$form->is_anon) {
            $id = hash_hmac('sha256', $user->social_id, env('API_FORMS_USER_KEY', 'not_anon'));
        } elseif($user && $form->is_anon) {
            $id = hash_hmac('sha256', $user->social_id, env('API_FORMS_ANON_KEY', 'anon'));
        } else {
            $id = hash_hmac('sha256', $form->date, env('API_FORMS_ANON_KEY', 'anon'));
        }

        $id = substr($id, 0, 32);

        $author['auth'] = [
            'scheme' => !$user ? 'internal' : 'external',
            'id' => $id,
        ];

        if($user && $user->social) {
            $author['auth']['provider'] = $user->social;
        }

        if(!$form->is_anon && $user && ($user->email || $user->full_name)) {

            $cpoint = [];

            if($user->email) {
                $cpoint['email'] = $user->email;
            }
            if($user->full_name) {
                $cpoint['name'] = $user->full_name;

                //if(!$form->is_anon) {
                    $author['name'] = $user->full_name;
                //}
            }

            //$author['auth']['provider'] = $user->social;
            $author['contactPoint'] = $cpoint;
        }

        $payload['payload']['author'] = $author;

        $__payload = $form->getPayload();

        if(isset($__payload->payload)) {
            $__payload = $__payload->payload;
        }

        if($form->schema == 'comment') {
            if(isset($form->json->comment)) {
                $payload['payload']['comment'] = $form->json->comment;
            }
            elseif(isset($form->json->text)) {
                $payload['payload']['comment'] = $form->json->text;
            }
            elseif(isset($__payload->comment)) {
                $payload['payload']['comment'] = $__payload->comment;
            }
            elseif(isset($__payload->text)) {
                $payload['payload']['comment'] = $__payload->text;
            }
        }
        else {
            $payload['payload']['formData'] = isset($__payload->userForm) ? $__payload->userForm : $__payload->formData;
        }

        if($form->jsonParentForm || $parent) {
            $payload['payload']['parentForm'] = $parent ? $parent : $form->jsonParentForm;
        }

        if(isset($payload['payload']['formData']->generalComment)) {
            $payload['payload']['formData']->overallScoreComment = $payload['payload']['formData']->generalComment;
            unset($payload['payload']['formData']->generalComment);
        }
        if(isset($payload['payload']['formData']->generalScore)) {
            $payload['payload']['formData']->overallScore = $payload['payload']['formData']->generalScore;
            unset($payload['payload']['formData']->generalScore);
        }
        if(isset($payload['payload']['formData']->is_anon)) {
            unset($payload['payload']['formData']->is_anon);
        }

        $JsonFormController->recursive_sort($payload);

        return json_encode($payload, $JsonFormController->json_options);
    }

    public static function formsUpdate()
    {
        $data = file_get_contents(storage_path('api/mapping.txt'));
        preg_match_all('|\"[a-z0-9]+?\",\"[a-z0-9]+?\"|iU', $data, $ids, PREG_PATTERN_ORDER);
        $api = new Api();
        $forms = 0;

        if(!empty($ids[0])) {
            foreach($ids[0] as $id) {
                $id = explode(',', trim(strtr($id, ['""'=>'',' '=>''])));

                if(count($id) == 2 && $local_form = JsonForm::where('object_id', $id[0])->first()) {
                    if($api_form = $api->getForm($id[1])) {
                        $local_form->payload = json_encode($api_form->envelope, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
                        $local_form->object_id = $api_form->id;
                        $local_form->save();
                        $forms++;
                    }
                    sleep(1);
                }
            }
        }

        return $forms;
    }

    public static function reviewsNgo()
    {
        try {
            $reviews = JsonForm::groupBy('user_id')->get();
            $forms = 0;

            foreach ($reviews as $review) {
                if($review->user) {
                    $review->where('user_id', $review->user_id)->update(['ngo_profile_id' => $review->user->ngo_profile_id]);
                    $forms++;
                }
            }

            return $forms;
        } catch (\Exception $e){
            return 0;
        }
    }

    public static function reviewsUpdate()
    {
        file_put_contents(storage_path('app/reviewsUpdate.lock'), null);

        try {
            $reviews = JsonForm::orderby('date','desc')->groupBy('tender')->paginate(100);
            $forms = 0;

            foreach ($reviews as $review) {
                if ($tender = self::getSearchResults($review->tender, __FUNCTION__)) {
                    $tender = json_decode($tender);

                    if (!empty($tender->data)) {
                        $tender = $tender->data;
                    } else {
                        continue;
                    }

                    JsonForm::where('tender', $tender->id)
                        ->update([
                            'tender_id' => $tender->tenderID,
                            'price' => !empty($tender->value->amount) ? $tender->value->amount : 0,
                            'tender_status' => $tender->status,
                            'region' => (isset($tender->procuringEntity->address->postalCode) && is_numeric($tender->procuringEntity->address->postalCode)) ? self::getRegion($tender->procuringEntity->address->postalCode) : null,
                            'postal_code' => (isset($tender->procuringEntity->address->postalCode) && is_numeric($tender->procuringEntity->address->postalCode)) ? $tender->procuringEntity->address->postalCode : null,
                            'procurement_method_type' => !empty($tender->procurementMethodType) ? $tender->procurementMethodType : null,
                            'entity_id' => !empty($tender->procuringEntity->identifier->id) ? $tender->procuringEntity->identifier->id : null,
                            'tender_json' => self::getTenderJson($tender),
                        ]);

                    $forms++;
                    //usleep(100);
                    sleep(1);
                }
            }

            return $forms;
        } catch (\Exception $e){
            @unlink(storage_path('app/reviewsUpdate.lock'));
            return 0;
        }

        @unlink(storage_path('app/reviewsUpdate.lock'));
    }

    public static function getRegion($pcode)
    {
        $regions = json_decode(file_get_contents(public_path() . '/sources/'.Config::get('locales.current').'/region.json'));

        foreach($regions as $id => $name) {

            if(stripos($id, "-") !== FALSE) {
                $range = explode("-", $id);
            } else {
                $range[] = (int)$id;
                $range[] = (int)$id;
            }

            $pc = substr($pcode, 0, 2);

            for($i = $range[0]; $i <= $range[1]; $i++) {
                if($i == (int)$pc) {
                    return $id;
                }
            }
        }

        return null;
    }

    protected static function getTenderJson($tender)
    {
        return json_encode([
            'procuringEntity'=>[
                'name' => (!empty($tender->procuringEntity->identifier->legalName) ? $tender->procuringEntity->identifier->legalName : $tender->procuringEntity->name),
                'code' => isset($tender->procuringEntity->identifier) ? $tender->procuringEntity->identifier->id : '',
                'locality' => (!empty($tender->procuringEntity->address->locality) ? $tender->procuringEntity->address->locality : ''),
            ],
            'title' => $tender->title,
            'description' => !empty($tender->description) ? $tender->description : '',
            'tenderID' => isset($tender->tenderID) ? $tender->tenderID : '',
            'enquiryPeriod' => isset($tender->enquiryPeriod) ? $tender->enquiryPeriod : '',
            'tenderPeriod' => isset($tender->tenderPeriod) ? $tender->tenderPeriod : '',
            'value' => isset($tender->value) ? $tender->value : '',
            'status' => isset($tender->status) ? $tender->status : '',
        ], self::$json_options);
    }

    public static function getSearchResults($id, $function)
    {
        $request = head(\Symfony\Component\HttpFoundation\Request::createFromGlobals()->server);
        //$page = $request['REQUEST_SCHEME'].'://'.$request['HTTP_HOST'].@$request['REQUEST_URI'].@$request['QUERY_STRING'];

        $url = env('API_PUBLIC_TENDER') .'/'. $id;//.'&__url='.$function;

        //$header = get_headers($url)[0];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        if (env('API_LOGIN') && env('API_PASSWORD')) {
           // curl_setopt($ch, CURLOPT_USERPWD, env('API_LOGIN') . ":" . env('API_PASSWORD'));
            Log::info($url.' - '.env('API_LOGIN') . ":" . env('API_PASSWORD'));
        } else {
            Log::info($url);
        }

        $headers = [
            'X-Forwarded-For: '.@$request['REMOTE_ADDR'],
           // 'Accept-Encoding: gzip'
        ];

       // curl_setopt($ch,CURLOPT_ENCODING , "gzip");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public static function formsSyncFix()
    {
        $forms = JsonForm::where('model', 'LIKE', '%/%')->get();

        foreach($forms as $form)
        {
            $model=$form->model=='form/comment' ? 'comment' : 'form';
            $schema=$form->model=='form/comment' ? 'comment' : str_replace('form/tender', 'F', $form->model);

            $form->model=$model;
            $form->schema=$schema;

            $form->save();
        }

        return sizeof($forms);
    }

    public static function fixCommentsThread()
    {
        $forms = JsonForm::where('model', '=', 'comment')->whereNull('thread')->get();
        $count = 0;

        foreach($forms as $form)
        {
            $payload=json_decode($form->payload);

            if(!empty($payload->payload->parentForm)) {
                $count++;
                $form->thread=$payload->payload->parentForm;
            }else{
                echo 'No thread found: ' . $form->object_id . PHP_EOL;
            }

            $form->save();
        }

        return $count;
    }
}
