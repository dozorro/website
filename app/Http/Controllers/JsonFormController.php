<?php namespace App\Http\Controllers;

use App\Classes\Api;
use App\Models\Coin;
use Illuminate\Routing\Controller as BaseController;
use App\Classes\User;
use Carbon\Carbon;
use App\JsonForm;
use App\Helpers;
use App\Settings;
use Exception;
use Input;

class JsonFormController extends BaseController
{
    var $folder = '/sources/forms';

    var $forms = [
        'F101' => 'F101.json',
        'F102' => 'F102.json',
        'F103' => 'F103.json',
        'F104' => 'F104.json',
        'F105' => 'F105.json',
        'F106' => 'F106.json',
        'F107' => 'F107.json',
        'F108' => 'F108.json',
        'F109' => 'F109.json',
        'F110' => 'F110.json',
        'comment' => 'comment.json',
        'F111' => 'F111.json',
        'F112' => 'F112.json',
        'F201' => 'F201.json',
        'F202' => 'F202.json',
        'F203' => 'F203.json',
        'F204' => 'F204.json',
    ];

    private $F2X = [
        'F201',
        'F202',
        'F203',
        'F204',
    ];

    var $form;
    var $slug;
    private $input_form;

    public $json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;

	public function submit_form_ngo($slug)
	{
        $this->slug=$slug;

        $jsonFormPath = public_path().$this->folder.'/'.$this->forms[$slug];

        $update_field=@Settings::instance('perevorot.dozorro.form')->{$slug.'_field'};
        $update_code_field=@Settings::instance('perevorot.dozorro.form')->{$slug.'_code_field'};

        $ngo_form_data=Helpers::parseSettings(Settings::instance('perevorot.dozorro.form')->{$slug}, true);

        if(empty(Input::get('form')) || ((empty(Input::get('parents')) && Input::get('multy') == '0') || (empty(Input::get('tender_public_id')) && Input::get('multy') == '1')) )
        {
            response()->json(false, 200, [
                'Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'UTF-8'
            ], JSON_UNESCAPED_UNICODE);
        }

        if(!empty(Input::get('parents'))) {
            $parents = Input::get('parents');
            $tenders = JsonForm::whereIn('object_id', $parents)->get();
        } else {
            $parents = [null];
            $tenders = [];
        }

        foreach($parents as $parent) {
            foreach (Input::get('form') as $code => $value) {
                switch ($slug) {
                    case 'F201':
                        $prefix = 'abuse';
                        break;
                    case 'F202':
                        $prefix = 'action';
                        break;
                    case 'F203':
                        $prefix = 'result';
                        break;
                }

                $name = $ngo_form_data[$code];

                $this->input_form = [
                    $prefix . 'Code' => $code,
                    $prefix . 'Name' => $name,
                    $prefix . 'Comment' => $value,
                ];

                $jsonForm = new \App\Classes\JsonForm($jsonFormPath, $slug, $this->input_form);

                if (!$jsonForm->validate()) {
                    return response()->json(false, 200, [
                        'Content-Type' => 'application/json; charset=UTF-8',
                        'charset' => 'UTF-8'
                    ], JSON_UNESCAPED_UNICODE);
                }

                if(Input::get('multy') == '0') {
                    $this->payload($slug, 'form', $jsonForm, $parent, null, @Input::get('edit'));
                } else {
                    if($parent) {
                        $tender = array_first($tenders, function($key, $item) use($parent) {
                            return $parent == $item->object_id;
                        });

                        $this->payload($slug, 'form', $jsonForm, $parent, $tender->tender_id);
                    } else {
                        $tenders = explode(',', Input::get('tender_public_id'));

                        foreach($tenders as $tender_id) {
                            $this->payload($slug, 'form', $jsonForm, $parent, $tender_id);
                        }
                    }
                }
            }
        }

        return response()->json(true, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
	}

	public function submit($model)
	{
        $this->slug=$slug=Input::get('schema');

        if(in_array($this->slug, ['F201', 'F202', 'F203']))
            return $this->submit_form_ngo($this->slug);

		if(!in_array($slug, array_keys($this->forms)))
			abort(404);

        $jsonFormPath = public_path().$this->folder.'/'.$this->forms[$slug];
        $jsonForm = new \App\Classes\JsonForm($jsonFormPath, $slug);

        if (!$jsonForm->validate() && $slug != 'F204') {
            return response()->json(false, 200, [
                'Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'UTF-8'
            ], JSON_UNESCAPED_UNICODE);
        }

        $parents = !empty(Input::get('parents')) ? Input::get('parents') : [null];

        foreach($parents as $parent) {
            $response = $this->payload($slug, $model, $jsonForm, $parent);
        }

        return response()->json($response, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
	}

    function payload2($slug, $model, \App\Classes\JsonForm $jsonForm, $parent= null, $item, $f204)
    {
        if($item) {
            $tender=$item;

            $form = new JsonForm();
            $user = User::data();

            if ($user)
            {

                    $form->tender=$item->id;
                    $form->is_anon=0;
                    $form->schema=$slug;
                    $form->model=$model;
                    $form->owner=env('JSONFORMS_OWNER_ID', 'dozorro.org');
                    $form->thread=null;
                    $form->date=Carbon::now();
                    $form->payload=str_replace($f204->jsonParentForm, $parent, $f204->payload);
                    $p = json_decode($form->payload);
                    $p->date = Carbon::now()->toAtomString();
                    $form->payload = json_encode($p, $this->json_options);
                    $form->object_id=$this->hash_id($form->payload);
                    $form->entity_id=!empty($tender->procuringEntity->identifier->id) ? $tender->procuringEntity->identifier->id : null;
                    $form->tender_json=$this->getTenderJson($tender);
                    $form->author_json=$this->getAuthorJson($user);
                    $form->price = $tender->value->amount;
                    $form->tender_status = $tender->status;
                    $form->tender_id = $tender->tenderID;
                    $form->user_id = $user->user_id;
                    $form->ngo_profile_id = $user->user->ngo_profile_id;
                    $form->is_hide = 1;
                    $form->procurement_method_type = !empty($tender->procurementMethodType) ? $tender->procurementMethodType : null;

                    if(isset($tender->procuringEntity->address->postalCode) && is_numeric($tender->procuringEntity->address->postalCode)) {
                        $form->postal_code=$tender->procuringEntity->address->postalCode;
                        $form->set_region();
                    }

                    if(isset($tender->items) && !empty($tender->items)) {

                        $cpvs = [];

                        foreach ($tender->items AS $item) {
                            if(!in_array($item->classification->id, $cpvs)) {
                                $cpvs[] = $item->classification->id;
                            }
                        }

                        if(!empty($cpvs)) {
                            $form->cpv = implode(',', $cpvs);
                        }
                    }

                    $form->save();

                $response = true;
            } else {
                $response = false;
            }
        } else {
            $response = false;
        }

        return $response;
    }

	protected function payload($slug, $model, \App\Classes\JsonForm $jsonForm, $parent= null, $tender_id = null, $edit = false)
    {
        $user = User::data();

        if($user && (!empty($edit) && is_numeric($edit))) {

            $form = JsonForm::find($edit);
            $data = json_decode($form->getOriginal('payload'));
            $comment = current(Input::get('form'));
            $code = current(array_keys(Input::get('form')));
            $name = '';

            $update_field=@Settings::instance('perevorot.dozorro.form')->{$slug.'_field'};
            $update_code_field=@Settings::instance('perevorot.dozorro.form')->{$slug.'_code_field'};

            if(!empty($update_field) && !empty($update_code_field))
            {
                $codes=Helpers::parseSettings(Settings::instance('perevorot.dozorro.form')->{$slug}, true);
                $name=array_search($code, array_flip($codes));
            }

            if($slug == 'F202') {
                $data->payload->formData->actionComment = $comment;
                $data->payload->formData->actionCode = $code;
                $data->payload->formData->actionName = $name;
            }
            elseif($slug == 'F203') {
                $data->payload->formData->resultComment = $comment;
                $data->payload->formData->resultCode = $code;
                $data->payload->formData->resultName = $name;
            }

            $form->payload = json_encode($data, $this->json_options);
            $form->object_id=$this->hash_id($form->payload);
            $form->status = 3;
            $form->updated++;
            $form->save();

            if($form->schema == 'F202') {
                $user->user->ngo_profile->updateInbox($form);
            }

            return true;
        }

        $PageController=app('App\Http\Controllers\PageController');
        $PageController->search_type='tender';

        $json=$PageController->getSearchResults([
            'tid='.($tender_id ? $tender_id : Input::get('tender_public_id'))
        ]);

        $data=json_decode($json);

        if(!empty($data->items[0])) {
            $tender=$data->items[0];

            $form = new JsonForm();

            if ($user)
            {
                if(!Input::get('form') && empty($this->input_form))
                    return true;

                if($model=='form') {
                    $data = $this->data($jsonForm->getFormFile());
                } else {
                    $data = [
                        'comment'=>Input::get('form.comment')
                    ];
                }

                if (in_array($slug, ['form102', 'form103'])) {
                    $data = array_filter($data);
                }

                if (!empty($data)) {
                    $form->tender=$tender->id;
                    $form->is_anon=Input::get('form.is_anon') == 'true' ? 1 : 0;
                    $form->schema=$this->slug;
                    $form->model=$model;
                    $form->owner=env('JSONFORMS_OWNER_ID', 'dozorro.org');
                    $form->thread=Input::get('form.thread');
                    $form->date=Carbon::now();
                    $form->payload=$this->getPayload($data, $parent, $tender->id, $model);
                    $form->object_id=$this->hash_id($form->payload);
                    $form->entity_id=!empty($tender->procuringEntity->identifier->id) ? $tender->procuringEntity->identifier->id : null;
                    $form->tender_json=$this->getTenderJson($tender);
                    $form->author_json=$this->getAuthorJson($user);
                    $form->price = @$tender->value->amount;
                    $form->tender_status = $tender->status;
                    $form->tender_id = $tender->tenderID;
                    $form->user_id = $user->user_id;
                    $form->ngo_profile_id = $user->user->ngo_profile_id;
                    $form->procurement_method_type = !empty($tender->procurementMethodType) ? $tender->procurementMethodType : null;
                    $form->was_sent=0;
                    $form->is_customer=0;
                    $form->status = in_array($this->slug, ['F202', 'F203']) ? 0 : 1;

                    if(!empty($data['abuseCode']) && $data['abuseCode'] == 'A028' && $form->schema == 'F201') {
                        $form->status = 0;
                    }

                    if(isset($tender->procuringEntity->address->postalCode) && is_numeric($tender->procuringEntity->address->postalCode)) {
                        $form->postal_code=$tender->procuringEntity->address->postalCode;
                        $form->set_region();
                    }

                    if(isset($tender->items) && !empty($tender->items)) {
                        $cpvs = [];

                        foreach ($tender->items AS $item) {
                            if(!in_array($item->classification->id, $cpvs)) {
                                $cpvs[] = $item->classification->id;
                            }
                        }

                        if(!empty($cpvs)) {
                            $form->cpv = implode(',', $cpvs);
                        }
                    }

                    if(in_array($this->slug, $this->F2X)) {
                        $send = (bool) env('API_FORMS_SEND_F2X', false);
                    } else {
                        $send = (bool) env('API_FORMS_SEND', false);
                    }

                    if($this->slug=='comment' && !empty($form->thread)) {
                        $_form = JsonForm::where('object_id', $form->thread)->first();

                        if(in_array($_form->schema, $this->F2X)) {
                            $send = (bool) env('API_FORMS_SEND_F2X', false);
                        } else {
                            $send = (bool) env('API_FORMS_SEND', false);
                        }
                    }

                    if($send) {
                        $api = new Api();

                        $response=$api->sendForm($form);

                        if(empty($response->error)) {
                            $form->was_sent = !empty($response);
                            $response='ok';

                            try {
                                $form->save();
                                $form->checkIsCustomer();

                                if($form->schema == 'F202') {
                                    $user->user->ngo_profile->updateInbox($form);
                                }
                                elseif(!empty($data['abuseCode']) && $data['abuseCode'] == 'A028' && $form->schema == 'F201') {
                                    $user->user->ngo_profile->updateInbox($form);
                                }
                            } catch(\Exception $e) {
                                return 'error: form already exists!';
                            }
                        }else{
                            return $response;
                        }
                    } else {
                        $response = true;
                        $form->was_sent=1;

                        try {
                            $form->save();
                            $form->checkIsCustomer();

                            if($form->schema == 'F202') {
                                $user->user->ngo_profile->updateInbox($form);
                            }
                            elseif(!empty($data['abuseCode']) && $data['abuseCode'] == 'A028' && $form->schema == 'F201') {
                                $user->user->ngo_profile->updateInbox($form);
                            }
                        } catch(\Exception $e) {
                            return 'error: form already exists!';
                        }
                    }

                    if(!$parent && isset($data['abuseCode']) && $prices = @Settings::instance('perevorot.dozorro.form')->F201_prices) {
                        $code = $data['abuseCode'];
                        $item = array_first($prices, function($k, $v) use($code) {
                            return $v['code'] == $code;
                        });

                        if($item && $price = @$item[($this->slug . "_price")]) {
                            $comment = @Settings::instance('perevorot.dozorro.form')->{$form->schema.'_formName'} . ' : ' . $data['abuseCode'].'='.$data['abuseName'] . ' : '.$form->tender_id . ' : ' . $form->object_id;
                            $coin = new Coin([
                                'object_id' => $form->object_id,
                                'sum' => $price,
                                'type' => 1,
                                'dt' => Carbon::now(),
                                'author' => 'Dozorro',
                                'comment' => $comment,
                            ]);
                            $user->user->ngo_profile->coin()->save($coin);
                        }
                    } elseif($parent) {
                        if($_form = JsonForm::where('object_id', $parent)->where('is_hide', 0)->first()) {
                            $prices = false;
                            $code = null;

                           if($_form->schema == 'F201') {
                               $code = @$_form->json->abuseCode;
                               $prices = @Settings::instance('perevorot.dozorro.form')->F201_prices;
                           } else if($_form->schema == 'F202') {
                               if($_form = JsonForm::where('object_id', $_form->jsonParentForm)->where('is_hide', 0)->first()) {
                                   $code = @$_form->json->abuseCode;
                                   $prices = @Settings::instance('perevorot.dozorro.form')->F202_prices;
                               }
                           }

                           if($prices && $code) {
                               $item = array_first($prices, function ($k, $v) use ($code) {
                                   return $v['code'] == $code;
                               });

                               if ($item && $price = @$item[($this->slug . "_price")]) {
                                   if(!isset($data['reason'])) {
                                       $data = array_filter($data, function($v, $k) {
                                           return stripos($k, 'Comment') === FALSE;
                                       }, ARRAY_FILTER_USE_BOTH);
                                       $_data = implode('=', $data);
                                   } else {
                                       $_data = 'reason='.$data['reason'];
                                   }

                                   $comment = @Settings::instance('perevorot.dozorro.form')->{$form->schema.'_formName'} . ' : ' . $_data . ' : '.$form->tender_id . ' : ' . $form->object_id;
                                   $coin = new Coin([
                                       'object_id' => $form->object_id,
                                       'sum' => $price,
                                       'type' => 1,
                                       'dt' => Carbon::now(),
                                       'author' => 'Dozorro',
                                       'comment' => $comment,
                                   ]);
                                   $user->user->ngo_profile->coin()->save($coin);
                               }
                           }
                        }
                    }
                } else {
                    $response = false;
                }
            } else {
                $response = false;
            }
        } else {
            $response = false;
        }

        return $response;
    }

    protected function getAuthorJson($user)
    {
        if(Input::get('form.is_anon') !== 'true') {
            $id = hash_hmac('sha256', $user->social_id, env('API_FORMS_USER_KEY', 'not_anon'));
        } else {
            $id = hash_hmac('sha256', $user->social_id, env('API_FORMS_ANON_KEY', 'anon'));
        }

        $id = substr($id, 0, 32);

        $author = [
            'auth' => [
                'scheme' => 'external',
                'provider' => $user->social,
                'id' => $id,
            ],
        ];

        if(Input::get('form.is_anon') !== 'true' && ($user->email || $user->full_name)) {

            $cpoint = [];

            if($user->email) {
                $cpoint['email'] = $user->email;
            }
            if($user->full_name) {
                $cpoint['name'] = $user->full_name;

                //if(Input::get('form.is_anon') !== 'true') {
                    $author['name'] = $user->full_name;
                //}
            }

            //if(Input::get('form.is_anon') !== 'true') {
                //$author['auth']['id'] = md5($user->social_id . Input::get('tender'));
            //}

            //$author['auth']['provider'] = $user->social;
            $author['contactPoint'] = $cpoint;
        }

        return json_encode($author, $this->json_options);
    }

    public function getTenderJson($tender)
    {
        return json_encode([
            'procuringEntity'=>[
                'name' => (!empty($tender->procuringEntity->identifier->legalName) ? $tender->procuringEntity->identifier->legalName : $tender->procuringEntity->name),
                'code' => $tender->procuringEntity->identifier->id,
                'locality' => (!empty($tender->procuringEntity->address->locality) ? $tender->procuringEntity->address->locality : ''),
            ],
            'title' => $tender->title,
            'description' => !empty($tender->description) ? $tender->description : '',
            'tenderID' => $tender->tenderID,
            'enquiryPeriod' => isset($tender->enquiryPeriod) ? $tender->enquiryPeriod : '',
            'tenderPeriod' => isset($tender->tenderPeriod) ? $tender->tenderPeriod : '',
            'value' => @$tender->value,
            'status' => $tender->status,
        ], $this->json_options);
    }

	protected function getPayload($formData, $parent = null, $tender_id = null, $model)
	{
    	$user = User::data();

        $update_field=@Settings::instance('perevorot.dozorro.form')->{$this->slug.'_field'};
        $update_code_field=@Settings::instance('perevorot.dozorro.form')->{$this->slug.'_code_field'};

        if(!empty($update_field) && !empty($update_code_field))
        {
            $data=Helpers::parseSettings(Settings::instance('perevorot.dozorro.form')->{$this->slug}, true);

            $formData[$update_code_field]=array_search($formData[$update_field], $data);
        }

    	$payload = [
    	        'owner' => env('JSONFORMS_OWNER_ID', 'dozorro.org'),
                'model' => 'form/'.str_replace('F', 'tender', $this->slug),
                'date' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())->toAtomString(),
    	        'payload' => [
                    'tender'=>$tender_id ? $tender_id : Input::get('tender'),
                    'formData'=>$formData
                ]
    	];

        $payload['payload']['author'] = json_decode($this->getAuthorJson($user));

        if($this->slug == 'comment') {
            $payload['payload']['comment'] = $formData['comment'];
            unset($payload['payload']['formData']);
        }

        if(!$parent) {
            if (!empty(Input::get('parent')) && in_array($this->slug, ['F202', 'F203', 'F204'])) {
                $payload['payload']['parentForm'] = Input::get('parent');
            } elseif(!empty(Input::get('form.thread'))) {
                $payload['payload']['parentForm'] = Input::get('form.thread');
            }
        } else {
            $payload['payload']['parentForm'] = $parent;
        }

        $this->recursive_sort($payload);

        $json=json_encode($payload, $this->json_options);

        return $json;
    }

    public function hash_id($data)
    {
		$h1 = hash('sha256', $data, true);
		$h2 = hash('sha256', $h1);

		return substr($h2, 0, 32);
	}

    public function recursive_sort(&$obj)
    {
		if (is_object($obj)){
			$obj = (array) $obj;
        }

		foreach ($obj as &$val)
		{
			if (is_array($val) || is_object($val)){
				$this->recursive_sort($val);
            }
        }

		ksort($obj);
	}

	protected function data($form)
	{
        $form=json_decode($form, true);

	    if (!array_key_exists('properties', $form) || !array_key_exists('formData', $form['properties'])) {
            throw Exception('Случилась какая-то ошибка');
        }

        $form=$form['properties']['formData'];

        $data =! empty($form['properties']) ? array_intersect_key(($this->input_form ? $this->input_form : Input::get('form')), $form['properties']) : [];

        return $data;
    }

    public function jsonForms($slug, $edrpou = null)
    {
        if(!in_array($slug, array_keys($this->forms)))
            abort(404);

        $jsonFormPath = public_path().'/sources/forms/'.$this->forms[$slug];

        $json=json_decode(file_get_contents($jsonFormPath));

        // add is_anon
        $user = User::data();

        if($edrpou && isset($user->user) && !$user->user->ngo_profile && !$user->user->issetEdrpou($edrpou)) {
            $form = new \stdClass();
            $form->key = "is_anon";
            $form->inlinetitle = t('tender.forms.is_anon');

            $json->properties->formData->form[] = $form;

            $prop = new \stdClass();
            $prop->type = "boolean";
            $props = (array)$json->properties->formData->properties;
            $props['is_anon'] = $prop;

            $json->properties->formData->properties = (object)$props;
        }

        $data=Helpers::parseSettings(@Settings::instance('perevorot.dozorro.form')->{$slug});
        $update_field=@Settings::instance('perevorot.dozorro.form')->{$slug.'_field'};

        if(!empty($data) && !empty($update_field) && !empty($json->properties->formData->form))
        {
            foreach($json->properties->formData->form as $field)
            {
                if($field->key==$update_field){
                    $field->options=$data;
                }
            }
        }

        return response()->json($json, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function json($slug = null)
    {
		if(!in_array($slug, array_keys($this->forms)))
			abort(404);

        $jsonFormPath = storage_path().'/forms/'.$this->forms[$slug];

        $json=json_decode(file_get_contents($jsonFormPath));

        $data=Helpers::parseSettings(@Settings::instance('perevorot.dozorro.form')->{$slug});
        $update_field=@Settings::instance('perevorot.dozorro.form')->{$slug.'_field'};

        if(!empty($data) && !empty($update_field) && !empty($json->properties->formData->form))
        {
            foreach($json->properties->formData->form as $field)
            {
                if($field->key==$update_field){
                    $field->options=$data;
                }
            }
        }

        return response()->json($json, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }
}
