<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\JsonForm;
use App\Models\MvpTemplate;
use App\Models\Risk;
use App\Models\RiskFeedback;
use App\Models\RiskValue;
use App\Models\User;
use App\Settings;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Input;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    private $awardsRisks = ['R1080','R2010','R2030','R2040','R2050','R3010','R3020','R3040','R3050','R3060','R3070','R3080'];
    public $profileAccess;
    public $profileRole1TplId;
    public $profileRole2TplId;

    public function riskComment(Request $request) {
        if($request->has('risk_value') && $request->has('comment')) {
            $newComment = new RiskFeedback();

            $newComment->risk_code = $request->get('risk_code');
            $newComment->tender_id = $request->get('tender_id');
            $newComment->risk_evaluation = $request->get('risk_value');
            $newComment->risk_comment = $request->get('comment');
            $newComment->ngo_user_id = $request->get('email');
            $newComment->ngo_profile_id = $request->get('user_id');
            $newComment->full_name = $request->get('full_name');
            $newComment->date_submitted = Carbon::now();

            $newComment->save();

            $status = 'ok';
        } else {
            $status = 'error';
        }

        return response()->json([
            'status' => $status
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function setProfileTpl()
    {
        $this->profileAccess = @Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->profile_access;

        $tpls = MvpTemplate::where('is_default', 1)->whereIn('role', [1, 2])->get();
        $role1 = array_first($tpls, function ($key, $item) {
            return $item->role == 1;
        });
        $role2 = array_first($tpls, function ($key, $item) {
            return $item->role == 2;
        });

        $this->profileRole1TplId = !empty($role1) ? $role1->id : 0;
        $this->profileRole2TplId = !empty($role2) ? $role2->id : 0;
    }

    public function tendersSidebarBlock(Request $request, $block)
    {
        $tenderID=$request->get('id');

        if(!$tenderID){
            abort(404);
        }

        if(method_exists($this, 'tendersSidebar'.ucfirst($block))){
            return $this->{'tendersSidebar'.ucfirst($block)}($request);
        }else{
            abort(404);
        }
    }

    public function tendersSidebarItems(Request $request)
    {
        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $parser->modifiers=[
            'get_items',
        ];

        $item=$parser->tender_parse($tenderID, null, true);

        $items=[];
        $other=[];

        if($item->__isMultiLot) {
            foreach ($item->lots as $one) {
                if($one->id == $request->get("lot_id") && !empty($one->__items)) {

                    $other=[
                        '__items_deliveryDate' => @$one->__items_deliveryDate,
                        '__items_address' => @$one->__items_address,
                    ];

                    foreach($one->__items as $_item) {
                        array_push($items, [
                            'description' => @$_item->description,
                            'cpv' => @$_item->classification->id,
                            'hidden' => true,
                            'quantity' => @$_item->quantity,
                            'unit_name' => @$_item->unit->name,
                            'cpv_description' => @$_item->classification->description,
                            '__address' => @$_item->__address,
                            '__format_delivery_date' => @$_item->__format_delivery_date,
                            'maxHeight' => 110,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'items' => $items,
            'other' => $other,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarLots(Request $request)
    {
        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $parser->modifiers=[
            'get_initial_bids',
            'get_initial_bids_dates',
            'get_bids',
            'get_lots',
        ];

        $item=$parser->tender_parse($tenderID, null, true);

        $lot=null;
        
        if($item->__isMultiLot) {
            foreach ($item->lots as $one) {
                if($one->id == $request->get("lot_id")) {
                    $lot = [
                        'description' => @$one->description,
                        'status' => $one->__status_name,
                    ];
                }
            }
        }

        return response()->json([
            'lot' => $lot,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarReviews(Request $request)
    {
        $tenderID=$request->get('id');

        $reviews = JsonForm::
            byTender($tenderID)
            ->where('is_hide', 0)
            ->where('model', '=', 'form')
            ->whereIn('schema', ['F101','F102','F111','F114','F115','F116'])
            ->orderBy('date', 'DESC')
            ->get();

        Helpers::parseUserData($reviews);
        Helpers::parseUserRelationData($reviews);

        $review_comments=JsonForm::where('schema', 'comment')
            ->whereIn('thread', array_pluck($reviews, 'object_id'))
            ->get();

        Helpers::parseUserData($review_comments);
        Helpers::parseUserRelationData($review_comments);

        $review_comments_by_object_id=[];

        foreach($review_comments as $comment) {
            if(empty($review_comments_by_object_id[$comment->thread])){
                $review_comments_by_object_id[$comment->thread]=[];
            }

            $review_comments_by_object_id[$comment->thread][]=$comment;
        }

        $_reviews = [];

        foreach($reviews as $review) {
            $review->__comments=!empty($review_comments_by_object_id[$review->object_id]) ? $review_comments_by_object_id[$review->object_id] : [];
            $group = $review->showGroup();
            $comments = [];

            if(!empty($review->__comments)) {
                foreach($review->__comments as $comment) {

                    $label = false;

                    if($comment->__user && empty($comment->userGroup) && $comment->__user->issetEdrpou($comment->entity_id)) {
                        $label = t('tender.answer_customer');
                    }
                    elseif($comment->__user && !empty($comment->userGroup) && $comment->__user->issetEdrpou($comment->entity_id)) {
                        $label = $comment->userGroup->name;
                    }

                    $json = $comment->json;

                    foreach($json as $key => $value) {
                        if(ends_with($key, 'Comment')) {
                            $json->$key = auto_format($value);
                        }
                    }

                    $anon = true;

                    if($comment->__user && ($comment->__user->ngo_profile || $comment->__user->issetEdrpou($comment->entity_id))) {
                        $anon = false;
                    } else {
                        if($comment->is_anon) {
                            $anon = true;
                        } else {
                            $anon = false;
                        }
                    }

                    array_push($comments, [
                        'label' => $label,
                        'user' => !$anon ? $comment->showAuthorName() : t('tender.contact_information_hidden'),
                        'schema' => $comment->schema,
                        'date' => $comment->date->format('d.m.Y H:i'),
                        'json' => $json,
                        'maxHeight' => 110,
                    ]);
                }
            }

            $json = $review->json;

            foreach($json as $key => $value) {
                if(ends_with($key, 'Comment')) {
                    $json->$key = auto_format($value);
                }
            }

            $anon = true;

            if($review->__user && ($review->__user->ngo_profile || $review->__user->issetEdrpou($review->entity_id))) {
                $anon = false;
            } else {
                if($review->is_anon) {
                    $anon = true;
                } else {
                    $anon = false;
                }
            }

            array_push($_reviews, [
                'label' => !empty($group) ? $group->name : false,
                'user' => !$anon ? $review->showAuthorName() : t('tender.contact_information_hidden'),
                'schema' => $review->schema,
                'date' => $review->date->format('d.m.Y H:i'),
                'json' => $json,
                'comments' => $comments,
                'maxHeight' => 110,
            ]);
        }

        return response()->json([
            'reviews' => $_reviews,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarNgo(Request $request)
    {
        $tenderID=$request->get('id');

        $forms = JsonForm::with('ngo_profile')
            ->byTender($tenderID)
            ->whereNotNull('ngo_profile_id')
            ->where('is_hide', 0)
            ->byForm(true)
            ->byFormStatus(1)
            ->orderBy('schema')
            ->get()
            ->groupBy('ngo_profile_id');

        $ngos = new Collection([]);

        foreach($forms as $ngoId => $_forms) {
            $ngo = $_forms->first()->ngo_profile;
            $ngo->forms = $_forms;
            $ngos->push($ngo);
        }

        unset($forms);
        $ngos = $ngos->keyBy('id');
        $_ngos = collect();

        foreach($ngos as &$ngo) {

            $f201 = new \stdClass();
            $f201 = [];
            $f202 = new \stdClass();
            $f202 = [];
            $f203 = new \stdClass();
            $f203 = [];
            $f204 = new \stdClass();
            $f204 = [];

            foreach($ngo->forms as $form) {
                if ($form->schema == 'F201') {
                    $abuseComment = @$form->json->abuseComment;

                    if(!empty($abuseComment) && strpos($abuseComment, '{DOWNLOAD_URL}')) {
                        $abuseComment = str_replace('{DOWNLOAD_URL}', config('services.localstorage.url'), $abuseComment);
                        $abuseComment = str_replace('">/files/download/', '">', $abuseComment);
                    }

                    $f201[] = [
                        'id' => $form->object_id,
                        'name' => $form->json->abuseName,
                        'comment' => $abuseComment,
                        'hidden' => true,
                    ];
                } elseif($form->schema == 'F202') {

                    $actionComment = @$form->json->actionComment;

                    if(!empty($actionComment) && strpos($actionComment, '{DOWNLOAD_URL}')) {
                        $actionComment = str_replace('{DOWNLOAD_URL}', config('services.localstorage.url'), $actionComment);
                        $actionComment = str_replace('">/files/download/', '">', $actionComment);
                    }

                    $f202[$form->JsonParentForm][] = [
                        'id' => $form->object_id,
                        'parent' => $form->JsonParentForm,
                        'name' => $form->json->actionName,
                        'comment' => $actionComment,
                        'hidden' => true,
                    ];
                } elseif($form->schema == 'F203') {
                    $resultComment = $form->json->resultComment;

                    if(!empty($resultComment) && strpos($resultComment, '{DOWNLOAD_URL}')) {
                        $resultComment = str_replace('{DOWNLOAD_URL}', config('services.localstorage.url'), $resultComment);
                        $resultComment = str_replace('">/files/download/', '">', $resultComment);
                    }

                    $f203[$form->JsonParentForm][] = [
                        'id' => $form->object_id,
                        'parent' => $form->JsonParentForm,
                        'name' => $form->json->resultName,
                        'comment' => @$resultComment,
                        'hidden' => true,
                    ];
                } elseif($form->schema == 'F204') {

                    $name = $form->json->reason;

                    $f204[$form->JsonParentForm][] = [
                        'id' => $form->object_id,
                        'parent' => $form->JsonParentForm,
                        'name' => $name,
                        'hidden' => true,
                    ];
                }
            }

            $_ngos->push([
                'id' => $ngo->id,
                'name' => $ngo->title,
                'f201' => $f201,
                'f202' => $f202,
                'f203' => $f203,
                'f204' => $f204
            ]);
        }

        //print_r($_ngos);exit;
        return response()->json([
            'ngo' => $_ngos,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarFeatures(Request $request)
    {
        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $item=$parser->tender_parse($tenderID, null, true);
        $features = [];
        $lot = null;

        foreach ($item->lots as $_lot) {
            if($request->has('lot_id') && $request->get('lot_id') == $_lot->id) {
                $lot = $_lot;
                break;
            }
        }

        $parser->get_features($item, $lot);

        if(!empty($lot->__features)) {
            foreach($lot->__features as $feature) {
                array_push($features, [
                        'title' => $feature->title,
                        'enum' => @$feature->enum,
                    ]);
            }
        }

        return response()->json([
            'other' => [
                '__features_price_real' => $lot->__features_price_real
            ],
            'features' => $features,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarComplaints(Request $request)
    {
        $this->setProfileTpl();
        
        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $item=$parser->tender_parse($tenderID, null, true);

        if(!$request->has('lot_id')) {
            $complaintsTypes = $parser->get_all_complaints($item, null, true);
        }
        else {
            $lot = null;

            foreach ($item->lots as $_lot) {
                if($request->get('lot_id') == $_lot->id) {
                    $lot = $_lot;
                    break;
                }
            }

            $complaintsTypes = $parser->get_all_complaints($item, $lot, true);
        }

        $complaintsData=[
            'complaints' => [],
            'qualificationComplaints' => [],
            'awardComplaints' => [],
        ];

        foreach($complaintsTypes as $type => $complaints) {

            if(empty($complaints)) {
                continue;
            }

            foreach ($complaints as $k => $complaint) {
                if (!empty($complaint->documents)) {
                    $complaint->__documents_owner = new \StdClass();
                    $complaint->__documents_reviewer = new \StdClass();
                    $complaint->__documents_owner = [];
                    $complaint->__documents_reviewer = [];

                    foreach ($complaint->documents as $document) {
                        if ($document->author == 'complaint_owner') {
                            $complaint->__documents_owner[] = $document;
                        } elseif (in_array($document->author, ['aboveThresholdReviewers', 'reviewers'])) {
                            $complaint->__documents_reviewer[] = $document;
                        }
                    }
                }

                $documentsOwner = [];
                $documentsReviewer = [];

                if (!empty($complaint->__documents_owner)) {
                    foreach ($complaint->__documents_owner as $document) {
                        if (empty($document->stroked)) {
                            $documentsOwner[] = [
                                'id' => $document->id,
                                'url' => $document->url,
                                'title' => ($document->title == 'sign.p7s' ? t('tender.digital_signature') : $document->title),
                                'date' => !empty($document->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                            ];
                        }
                    }
                }
                if (!empty($complaint->__documents_reviewer)) {
                    foreach ($complaint->__documents_reviewer as $document) {
                        if (empty($document->stroked)) {
                            $documentsReviewer[] = [
                                'id' => $document->id,
                                'url' => $document->url,
                                'title' => ($document->title == 'sign.p7s' ? t('tender.digital_signature') : $document->title),
                                'date' => !empty($document->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                            ];
                        }
                    }
                }

                $key = ($item->procurementMethodType != 'belowThreshold' ? '!' : '') . 'belowThreshold';
                $status_key = $complaint->status;

                if ($complaint->status == 'stopping') {
                    $status_key = $complaint->status . (!empty($complaint->dateAccepted) ? '+' : '-') . 'dateAccepted';
                }

                $complaintsData[$type][] = [
                    'description' => $complaint->description,
                    'authorName' => @$complaint->author->contactPoint->name,
                    'authorUrl' => route('page.profile_by_id', [
                        'scheme' => @$complaint->author->identifier->scheme . '-' . @$complaint->author->identifier->id,
                        'tpl' => $this->profileRole2TplId,
                        'role' => 'role2'
                    ]),
                    'authorId' => @$complaint->author->identifier->id,
                    'date' => !empty($complaint->dateSubmitted) ? Carbon::createFromTimestamp(strtotime($complaint->dateSubmitted))->format('d.m.Y H:i') : ('tender.no_date'),
                    'status' => t('tender.complaints_statuses.' . $key . '.' . $status_key),
                    'hiddenDocumentsOwner' => true,
                    'hiddenDocumentsReviewer' => true,
                    'documentsOwner' => $documentsOwner,
                    'documentsReviewer' => $documentsReviewer,
                    'maxHeight' => 110,
                ];
            }
        }

        return response()->json([
            'complaints' => $complaintsData,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarQuestions(Request $request)
    {
        $this->setProfileTpl();

        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $parser->modifiers=[
            'get_items',
            'get_questions',            
        ];

        $item=$parser->tender_parse($tenderID, null, true);

        $questions=[];
        $_questions=[];
        $otherQuestions=[];

        if($item->__isMultiLot && $request->has('lot_id')) {
            foreach($item->lots as $lot) {
                if($lot->id == $request->get('lot_id')) {
                    $_questions=$parser->get_questions_lots($item, $lot);
                    $otherQuestions = $parser->get_other_questions_lots($item, $lot);
                    break;
                }
            }
        } else {
            $otherQuestions = $parser->get_other_questions($item);
            $_questions = @$item->__questions;
        }

        if(!empty($_questions)) {
            foreach($_questions as $question) {
                array_push($questions, [
                    'description' => $question->description,
                    'authorName' => @$question->author->contactPoint->name,
                    'authorUrl' => route('page.profile_by_id', ['scheme'=>@$question->author->identifier->scheme.'-'.@$question->author->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                    'authorId' => @$question->author->identifier->id,
                    'date' => Carbon::createFromTimestamp(strtotime($question->date))->format('d.m.Y H:i'),
                    'answer' => @$question->answer,
                    'dateAnswered' => !empty($question->dateAnswered) ? Carbon::createFromTimestamp(strtotime($question->dateAnswered))->format('d.m.Y H:i') : t('tender.no_date'),
                    'maxHeight' => 110,
                    'answerMaxHeight' => 110,
                ]);
            }
        }

        if(!empty($otherQuestions)) {
            foreach($otherQuestions as $complaint) {
                array_push($questions, [
                    'description' => $complaint->description,
                    'authorName' => @$complaint->author->contactPoint->name,
                    'authorUrl' => route('page.profile_by_id', ['scheme'=>@$complaint->author->identifier->scheme.'-'.@$complaint->author->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                    'authorId' => @$complaint->author->identifier->id,
                    'date' => !empty($complaint->dateSubmitted) ? Carbon::createFromTimestamp(strtotime($complaint->dateSubmitted))->format('d.m.Y H:i') : t('tender.no_date'),
                    'answer' => @$complaint->resolution,
                    'dateAnswered' => !empty($complaint->dateAnswered) ? Carbon::createFromTimestamp(strtotime($complaint->dateAnswered))->format('d.m.Y H:i') : t('tender.no_date'),
                    'maxHeight' => 110,
                    'answerMaxHeight' => 110,
                ]);
            }
        }

        /*
        if($item->procurementMethodType != 'belowThreshold') {
            if(!empty($item->complaints)) {
                foreach($item->complaints as $complaint) {
                    if($complaint->type == 'claim') {
                        array_push($questions, [
                            'description' => $complaint->description,
                            'authorName' => @$complaint->author->contactPoint->name,
                            'authorUrl' => route('page.profile_by_id', ['scheme'=>@$complaint->author->identifier->scheme.'-'.@$complaint->author->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                            'authorId' => @$complaint->author->identifier->id,
                            'date' => !empty($complaint->dateSubmitted) ? Carbon::createFromTimestamp(strtotime($complaint->dateSubmitted))->format('d.m.Y H:i') : t('tender.no_date'),
                            'answer' => @$complaint->resolution,
                            'dateAnswered' => !empty($complaint->dateAnswered) ? Carbon::createFromTimestamp(strtotime($complaint->dateAnswered))->format('d.m.Y H:i') : t('tender.no_date'),
                            'maxHeight' => 110,
                            'answerMaxHeight' => 110,
                        ]);
                    }
                }
            }
            if(!empty($item->qualifications)) {
                $qComplaints = [];

                foreach($item->qualifications as $qualification) {
                    if(!empty($qualification->complaints)) {
                        $qComplaints = array_merge($qComplaints, $qualification->complaints);
                    }
                }

                if(!empty($qComplaints)) {
                    foreach($qComplaints as $complaint) {
                        if($complaint->type == 'claim') {
                            array_push($questions, [
                                'description' => $complaint->description,
                                'authorName' => @$complaint->author->contactPoint->name,
                                'authorUrl' => route('page.profile_by_id', ['scheme'=>@$complaint->author->identifier->scheme.'-'.@$complaint->author->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                                'authorId' => @$complaint->author->identifier->id,
                                'date' => !empty($complaint->dateSubmitted) ? Carbon::createFromTimestamp(strtotime($complaint->dateSubmitted))->format('d.m.Y H:i') : t('tender.no_date'),
                                'answer' => @$complaint->resolution,
                                'dateAnswered' => !empty($complaint->dateAnswered) ? Carbon::createFromTimestamp(strtotime($complaint->dateAnswered))->format('d.m.Y H:i') : t('tender.no_date'),
                                'maxHeight' => 110,
                                'answerMaxHeight' => 110,
                            ]);
                        }
                    }
                }
            }
            if(!empty($item->awards)) {
                $aComplaints = [];

                foreach($item->awards as $award) {
                    if(!empty($award->complaints)) {
                        $aComplaints = array_merge($aComplaints, $award->complaints);
                    }
                }

                if(!empty($aComplaints)) {
                    foreach($aComplaints as $complaint) {
                        if($complaint->type == 'claim') {
                            array_push($questions, [
                                'description' => $complaint->description,
                                'authorName' => @$complaint->author->contactPoint->name,
                                'authorUrl' => route('page.profile_by_id', ['scheme'=>@$complaint->author->identifier->scheme.'-'.@$complaint->author->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                                'authorId' => @$complaint->author->identifier->id,
                                'date' => !empty($complaint->dateSubmitted) ? Carbon::createFromTimestamp(strtotime($complaint->dateSubmitted))->format('d.m.Y H:i') : t('tender.no_date'),
                                'answer' => @$complaint->resolution,
                                'dateAnswered' => !empty($complaint->dateAnswered) ? Carbon::createFromTimestamp(strtotime($complaint->dateAnswered))->format('d.m.Y H:i') : t('tender.no_date'),
                                'maxHeight' => 110,
                                'answerMaxHeight' => 110,
                            ]);
                        }
                    }
                }
            }
        }*/

        return response()->json([
            'questions' => $questions,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarDocuments(Request $request)
    {
        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $parser->modifiers=[
            'get_yaml_documents',
            'get_tender_documents',
        ];
        
        $item=$parser->tender_parse($tenderID, null, true);
        
        $documents=[];
        $_documents=[];

        if($item->__isMultiLot && $request->has('lot_id')) {
            foreach($item->documents as $key => $document) {
                if ($document->documentOf == 'tender') {
                    continue;
                } elseif ($document->documentOf == 'lot' && $request->get('lot_id') != $document->relatedItem) {
                    continue;
                } elseif ($document->documentOf == 'lot' && $request->get('lot_id') == $document->relatedItem) {
                    $_documents[] = $document;
                } else {
                    continue;
                }
            }
        } else {
            $_documents = $item->__tender_documents;
        }

        foreach($_documents as $document){
            if(empty($document->stroked)) {
                $documents[]=[
                    'id' => $document->id,
                    'url' => $document->url,
                    'title' => $document->title,//($document->title=='sign.p7s' ? t('tender.digital_signature') : $document->title),
                    'date' => !empty($document->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                ];
            }
        }

        $stroked=[];

        foreach($documents as $k=>$document){
            foreach($item->documents as $d) {
                if($d->id==$document['id'] && !empty($d->stroked)) {
                    $stroked[]=[
                        'd' => $k,
                        'url' => $d->url,
                        'title' => ($d->title=='sign.p7s' ? t('tender.digital_signature') : $d->title),
                        'date' => !empty($d->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($d->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                    ];
                }
            }
        }

        foreach($documents as $k=>$document) {
            unset($documents[$k]['id']);
        }

        $documents_five = [];
        $documents_all = [];

        foreach($documents as $document) {
            if(count($documents_five) < 5 && !strpos($document['title'], '.p7s')) {
                $documents_five[] = $document;
            }
            if(!strpos($document['title'], '.p7s')) {
                $documents_all[] = $document;
            }
        }

        return response()->json([
            'documents_five' => $documents_five,
            'documents_all' => $documents_all,
            'documents' => $documents,
            'stroked' => $stroked,
            'off' => true,
            'hide_documents_five' => false,
            'hide_documents_all' => true,
            'hide_documents' => true,
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarContracts(Request $request)
    {
        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $parser->modifiers=[
            'get_contracts',
            'get_contracts_changes',
            'get_contracts_ongoing',
            'get_signed_contracts',
        ];
        
        $item=$parser->tender_parse($tenderID, null, true);
        
        $contracts=[];

        if(!empty($item->__contracts)) {

            $_contracts = $item->__contracts;

            if($item->__isMultiLot && $request->has('lot_id')) {
                foreach($item->lots as $lot) {
                    if (!empty($lot->__signed_contracts) && $request->get('lot_id') == $lot->id) {
                        $_contracts = $lot->__signed_contracts;
                        break;
                    }
                }
            }

            foreach($_contracts as $contract) {
                if($contract->status == 'active') {
                    $documents=[];

                    if(!empty($contract->documents)) {
                        foreach($contract->documents as $document) {
                            array_push($documents, [
                                'url'=>$document->url,
                                'title'=>($document->title=='sign.p7s' ? t('tender.digital_signature') : $document->title),
                                'date'=>!empty($document->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                            ]);
                        }
                    }
                    
                    $changes=[];

                    if(!empty($contract->__changes)) {
                        foreach($contract->__changes as $change) {
                            $change_contracts=[];

                            if(!empty($change->contract)) {
                                foreach($change->contract as $change_contract) {
                                    array_push($change_contracts, [
                                        'url'=>$change_contract->url,
                                        'title'=>($change_contract->title=='sign.p7s' ? t('tender.digital_signature') : $change_contract->title),
                                        'date'=>!empty($change_contract->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($change_contract->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                                    ]);
                                }
                            }

                            array_push($changes, [
                                'rationale'=>$change->rationale,
                                'rationaleTypes'=>$change->rationaleTypes,
                                'date'=>@ \Carbon\Carbon::createFromTimestamp(strtotime($change->date))->format('d.m.Y H:i'),
                                'contracts'=>$change_contracts,
                                'count'=>sizeof($change_contracts),
                                'hidden' => true,
                            ]);
                        }
                    }

                    array_push($contracts, [
                        'price'=>$contract->__full_formated_price,
                        'documents'=>$documents,
                        'count'=>sizeof($documents),
                        'changes_count'=>(!empty($contract->__changes) ? sizeof($contract->__changes) : 0),
                        'changes'=>$changes,
                        'hidden' => true,
                    ]);
                }
            }
        }

        return response()->json([
            'contracts' => $contracts,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarPreQualifications(Request $request)
    {
        $this->setProfileTpl();

        $tenderID = $request->get('id');

        $parser = app('App\Http\Controllers\PageController');

        $parser->modifiers = [
            'get_qualifications',
        ];

        $item=$parser->tender_parse($tenderID, null, true);

        if($item->procurementMethodType!='aboveThresholdEU') {
            return [];
        }

        $qualifications=[];

        if(!empty($item->__qualifications)) {
            foreach ($item->__qualifications as $k => $qualification) {

                if($item->__isMultiLot && !empty($qualification->lotID) && $request->has('lot_id')) {
                    if($request->get('lot_id') != $qualification->lotID) {
                        continue;
                    }
                }

                $documents = [];

                if (!empty($qualification->documents)) {
                    foreach ($qualification->documents as $document) {
                        array_push($documents, [
                            'url' => @$document->confidentiality != 'buyerOnly' ? $document->url : '',
                            'title' => ($document->title == 'sign.p7s' ? t('tender.digital_signature') : $document->title),
                        ]);
                    }
                }

                array_push($qualifications, [
                    'status'=>@$qualification->status,
                    'status_icon'=>$qualification->status == 'active' ? 'positive' : ($qualification->status == 'pending' ? 'pending' : 'negative'),
                    'supplier'=> @$qualification->__name,
                    'supplierUrl' => route('page.profile_by_id', ['scheme'=>@$qualification->__bid->tenderers[0]->identifier->scheme.'-'.@$qualification->__bid->tenderers[0]->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                    'amount'=>@$qualification->__bid->value->amount,
                    'hidden'=>true,
                    'contact'=>@$qualification->__bid->__contactPoint,
                    'address'=>@$qualification->__bid->__address,
                    'status_name'=>@$qualification->__status_name,
                    'date'=>@$qualification->__format_date,
                    'documents'=>$documents,
                ]);
            }
        }

        return response()->json([
            'prequalifications' => $qualifications,
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tendersSidebarQualifications(Request $request)
    {
        $this->setProfileTpl();

        $tenderID=$request->get('id');

        $parser=app('App\Http\Controllers\PageController');

        $parser->modifiers=[
            'get_initial_bids',
            'get_initial_bids_dates',
            'get_bids',
            'get_awards',
            'get_uniqie_awards',
            'get_uniqie_bids'
        ];
        
        $item=$parser->tender_parse($tenderID, null, true);
        
        $awards=[];

        if(!empty($item->awards)) {

            $ids = [];
            $tags = [];

            foreach($item->awards as $award) {
                if(!empty($award->suppliers[0]->identifier->id)) {
                    $ids[] = $award->suppliers[0]->identifier->id;
                }
            }

            if(!empty($ids) && Schema::hasTable('dozorro_risk_tags')) {
                $tags = DB::table('dozorro_risk_tags')->whereIn('id', $ids)->get();
            }

            foreach($item->awards as $award) {

                if($item->__isMultiLot && !empty($award->lotID) && $request->has('lot_id')) {
                    if($request->get('lot_id') != $award->lotID) {
                        continue;
                    }
                }

                $documents=[];
                $documents_five = [];
                $documents_all = [];
                $documents_bid=[];
                $documents_bid_five = [];
                $documents_bid_all = [];

                if(!empty($award->documents)) {
                    foreach($award->documents as $document) {
                        array_push($documents, [
                            'url'=>$document->url,
                            'title'=>$document->title,//($document->title=='sign.p7s' ? t('tender.digital_signature') : $document->title),
                            'date'=>!empty($document->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                        ]);
                    }
                }

                if(!empty($documents)) {
                    foreach ($documents as $document) {
                        if (count($documents_five) < 5 && !strpos($document['title'], '.p7s')) {
                            $documents_five[] = $document;
                        }
                        if (!strpos($document['title'], '.p7s')) {
                            $documents_all[] = $document;
                        }
                    }
                }

                if(!empty($award->__bid->documents)) {
                    foreach($award->__bid->documents as $document) {
                        array_push($documents_bid, [
                            'url'=>$document->url,
                            'title'=>$document->title,//($document->title=='sign.p7s' ? t('tender.digital_signature') : $document->title),
                            'date'=>!empty($document->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                        ]);
                    }
                }

                if(!empty($documents_bid)) {
                    foreach ($documents_bid as $document) {
                        if (count($documents_bid_five) < 5 && !strpos($document['title'], '.p7s')) {
                            $documents_bid_five[] = $document;
                        }
                        if (!strpos($document['title'], '.p7s')) {
                            $documents_bid_all[] = $document;
                        }
                    }
                }

                $_tags = [];

                if(!empty($tags) && !empty($award->suppliers[0]->identifier->id)) {
                    foreach($tags as $tag) {
                        if($tag->id == $award->suppliers[0]->identifier->id) {
                            $_tags[] = t('indicatiors.tenderer.tags.'.$tag->extraordinary);
                        }
                    }
                }

                array_push($awards, [
                    'tags' => $_tags,
                    'status'=>@$award->status,
                    'status_icon'=>$award->status == 'active' ? 'positive' : ($award->status == 'pending' ? 'pending' : 'negative'),
                    'supplier'=>@$award->suppliers[0]->name,
                    'supplierUrl' => route('page.profile_by_id', ['scheme'=>@$award->suppliers[0]->identifier->scheme.'-'.@$award->suppliers[0]->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                    'amount'=>@$award->value->amount,
                    'bid'=>@$item->__initial_bids[$award->bid_id],
                    'bid_date'=>@\Carbon\Carbon::createFromTimestamp(strtotime($item->__initial_bids_dates[$award->bid_id]))->format('d.m.Y H:i'),
                    'hidden'=>true,
                    'contact'=>@$award->__contactPoint,
                    'address'=>@$award->__address,
                    'status_name'=>!empty($award->__status_name_other) ? $award->__status_name_other : @$award->__status_name,
                    'date'=>@$award->__format_date,
                    'documents'=>$documents,
                    'documents_five' => $documents_five,
                    'documents_all' => $documents_all,
                    'documents_bid'=>$documents_bid,
                    'documents_bid_five' => $documents_bid_five,
                    'documents_bid_all' => $documents_bid_all,
                    'hide_documents_bid' => true,
                    'hide_documents_bid_all' => true,
                    'hide_documents_bid_five' => false,
                    'hide_documents' => true,
                    'hide_documents_all' => true,
                    'hide_documents_five' => false,
                ]);
            }
        }

        if(!empty($awards)) {
            $data_year = array();

            foreach ($awards as $key => $arr) {
                $data_year[$key] = $arr['amount'];
            }

            array_multisort($data_year, SORT_ASC, $awards);
        }

        $bids=[];

        if(!empty($item->__bids)) {

            $ids = [];
            $tags = [];

            foreach($item->__bids as $bid) {
                if(!empty($bid->tenderers[0]->identifier->id)) {
                    $ids[] = $bid->tenderers[0]->identifier->id;
                }
            }

            if(!empty($ids) && Schema::hasTable('dozorro_risk_tags')) {
                $tags = DB::table('dozorro_risk_tags')->whereIn('id', $ids)->get();
            }

            foreach($item->__bids as $bid) {
                if(empty($bid->__award)) {

                    if($item->__isMultiLot && !empty($award->lotID) && $request->has('lot_id')) {

                        $lot_bid = null;

                        foreach($bid->lotValues as $key => $value) {
                            if($value->relatedLot===$request->get('lot_id')) {
                                $lot_bid = $value;
                                break;
                            }
                        }

                        if(empty($lot_bid)) {
                            continue;
                        }
                    }

                    $documents=[];

                    if(!empty($bid->documents)) {
                        foreach($bid->documents as $document) {
                            array_push($documents, [
                                'url'=>$document->url,
                                'title'=>($document->title=='sign.p7s' ? t('tender.digital_signature') : $document->title),
                                'date'=>!empty($document->dateModified) ? \Carbon\Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i') : t('tender.no_date')
                            ]);
                        }
                    }

                    $_tags = [];

                    if(!empty($tags) && !empty($bid->tenderers[0]->identifier->id)) {
                        foreach($tags as $tag) {
                            if($tag->id == $bid->tenderers[0]->identifier->id) {
                                $_tags[] = t('indicatiors.tenderer.tags.'.$tag->extraordinary);
                            }
                        }
                    }

                    array_push($bids, [
                        'tags' => $_tags,
                        'name'=>@$bid->tenderers[0]->name,
                        'bidUrl' => route('page.profile_by_id', ['scheme'=>@$bid->tenderers[0]->identifier->scheme.'-'.@$bid->tenderers[0]->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']),
                        'bid'=>$item->__initial_bids[$bid->id],
                        'amount'=>@$bid->value->amount,
                        'date'=>@\Carbon\Carbon::createFromTimestamp(strtotime($item->__initial_bids_dates[$bid->id]))->format('d.m.Y'),
                        'contact'=>@$bid->__contactPoint,
                        'address'=>@$bid->__address,
                        'hidden'=>true,
                        'documents'=>$documents
                    ]);
                }
            }
        }

        return response()->json([
            'awards' => $awards,
            'bids' => $bids,
            'other' => [],
            'off' => true
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }
    
    /*
    public function renderTendersSideBar(Request $request)
    {
        $FormController = app('App\Http\Controllers\FormController');
        $FormController->search_type = 'tender';

        $ids = ['id=' . $request->get('id')];
        $json = $FormController->getSearchResults($ids, true);
        $data = json_decode($json);
        $item = null;

        if(!empty($data->items)) {
            $item = $data->items[0];
        } else {
            return;
        }

        //Cache::forget('tender-parse-'.$request->get('id'));
        $item = Cache::remember('tender-parse-'.$request->get('id'), 60, function() use($item) {
            if(!empty($item)) {
                $item = app('App\Http\Controllers\PageController')->tender_parse($item->tenderID, $item, false);
                //$item->__isMultiLot=true;

                $results = JsonForm::bySingleForm('F101')->byTender($item->tenderID)->get();

                if(!$results->isEmpty()) {
                    $item->__ratings = [
                        1=>0,
                        2=>0,
                        3=>0,
                        4=>0,
                        5=>0
                    ];

                    $total = 0;

                    foreach ($results as $value) {
                        if(!empty($value->json->overallScore) && isset($item->__ratings[$value->json->overallScore])) {
                            $item->__ratings[$value->json->overallScore] += 1;
                            $total += $value->json->overallScore;
                        }
                    }

                    $item->__ratings_avg = $total/$results->count();
                    $item->__ratings_total = $results->count();
                    $item->___ratings = (array)$item->__ratings;

                    arsort($item->___ratings);

                    $i = 100;
                    $j = 0;

                    foreach ($item->___ratings as &$rating) {
                        if ($j == 0) {
                            $i /= $rating;
                            $rating = 100;
                        } else {
                            $rating *= $i;
                        }

                        $j++;
                    }
                }

                if($riskValue = RiskValue::where('tender_id', $item->id)->first()) {
                    $item->__rating = $riskValue->risk_value;
                    $item->__awardRating = $riskValue->risk_value;

                    $r = explode(',', $riskValue->risk_flags);
                    $r = array_filter($r, function($v) {
                        return !starts_with($v, 'F');
                    });

                    $risks = Risk::whereIn('risk_code', $r)->get();

                    if(!$risks->isEmpty()) {
                        $item->__risksTitle = array_column($risks->toArray(), 'risk_title');
                    }

                    foreach($risks as $risk) {
                        if (in_array($risk->risk_code, $this->awardsRisks)) {
                            $item->__awardRisksTitle[] = $risk->risk_title;
                        }
                    }

                    if($item->__isMultiLot) {
                        $values = DB::table('dozorro_risk_values')
                            ->where('tender_id', $item->id)
                            ->whereNotNull('lot_id')
                            ->get();

                        if(!empty($values)) {

                            $r = array_column($values, 'risk_code');
                            $r = array_filter($r, function($v) {
                                return !starts_with($v, 'F');
                            });

                            $risks = Risk::whereIn('risk_code', $r)->get();

                            foreach($item->lots as $lot) {
                                $rv = array_where($values, function($k, $v) use($lot) {
                                    return $lot->id == $v->lot_id;
                                });
                                $rv = array_column($rv, 'risk_code');

                                foreach($risks as $risk) {
                                    if(in_array($risk->risk_code, $rv)) {
                                        $lot->__risksTitle[] = $risk->risk_title;
                                    }
                                }
                            }
                        }
                    }
                }

                $item->__totalHundredForms = JsonForm::byTender($item->tenderID)->ByForm(false)->count();

                $ngo = JsonForm::byTender($item->tenderID)->with('ngo_profile')->whereNotNull('ngo_profile_id')->groupBy('ngo_profile_id')->get();

                if(!$ngo->isEmpty()) {
                    $item->__ngo = array_column(array_column($ngo->toArray(), 'ngo_profile'), 'title');
                }
            }

            return $item;
        });

        return view('partials.sidebar.sidebar', [
            'item' => $item
        ])->render();
    }
    */

    public function deferUserType(Request $request)
    {
        if($request->ajax()) {
            $response = new Response(['Set cookie...']);

            return $response->withCookie(cookie(
                'user_type',
                'show_after_24',
                60 * 24,
                '/',
                $request->getHost()
            ));
        }
    }

    public function saveUserType(Request $request)
    {
        if($request->ajax()) {
            if(!empty($request->get('auth'))) {
                if($user = User::find($request->get('auth'))) {
                    $user->user_type = $request->get('user_type');
                    $user->save();

                    $response = new Response(['saved']);

                    return $response;
                }
            } else {
                $response = new Response(['Set cookie...']);

                return $response->withCookie(cookie(
                            'user_type',
                            $request->get('user_type'),
                            time() + (60 * 24 * 365),
                            '/',
                            $request->getHost()
                        ));
            }
        }
    }

    public function forms()
    {
        $limit=(int) Input::get('limit');
        $limit=in_array($limit, [30, 60, 90]) ? $limit : 30;

        $items=DB::table(DB::raw('perevorot_dozorro_json_forms as main'))
                    ->select('main.payload', 'main.object_id')
                    //->join(DB::raw('perevorot_dozorro_json_forms as `parent`'), 'main.thread', '=', 'parent.object_id')
                    ->where('main.schema', 'NOT LIKE', 'F2%')
                    //->where('parent.schema', 'NOT LIKE', 'F2%')
                    ->where('was_sent', true)
                    ->orderBy('main.date', 'DESC')
                    ->take($limit)
                    ->get();

        $array=[];

        foreach($items as $item)
        {
            $payload=json_decode($item->payload);

            array_push($array, $item->object_id.' '.$payload->date);
        }

        return response(implode(PHP_EOL, $array), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'charset' => 'UTF-8'
        ]);
    }

    public function tender_index()
    {
        $db=DB::table('perevorot_dozorro_json_forms')->select('tender_json', 'model', DB::raw('count(*) AS total'), DB::raw('MAX(date) AS date'))->where('tender_json', '!=', '')->groupBy('tender', 'model')->take(300)->get();

        if($db)
        {
            $data=[
                'found'=>true,
                'dozorro'=>[]
            ];

            foreach($db as $row)
            {
                $json=json_decode($row->tender_json);

                if(empty($data['dozorro'][$json->tenderID]))
                    $data['dozorro'][$json->tenderID]=[];

                $date=new \Datetime($row->date);

                $item[$row->model.'Count']=$row->total;
                $item['last'.ucfirst($row->model).'Date']=$date->format(\DateTime::ATOM);

                $data['dozorro'][$json->tenderID]=$item;
            }

            if(empty($data['dozorro']))
            {
                $data=[
                    'found'=>false
                ];
            }
            else
            {
                $items=[];

                foreach($data['dozorro'] as $tenderID=>$row)
                {
                    $row['tenderID']=$tenderID;
                    $row=array_reverse($row);

                    array_push($items, $row);
                }

                $data['dozorro']=$items;
            }
        }
        else
        {
            $data=[
                'found'=>false
            ];
        }

        return response()->json([
            'data'=>$data
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function tender($id)
    {
        $db=DB::table('perevorot_dozorro_json_forms')->select('tender_json', 'model', DB::raw('count(*) AS total'), DB::raw('MAX(date) AS date'))->where('tender', $id)->groupBy('tender', 'model')->get();

        if($db)
        {
            $json=json_decode(head($db)->tender_json);

            $data=[
                'found'=>true,
                'id'=>$id,
                'tenderID'=>$json->tenderID,
                'dozorro'=>[]
            ];

            foreach($db as $row)
            {
                $date=new \Datetime($row->date);

                $data['dozorro'][$row->model.'Count']=$row->total;
                $data['dozorro']['last'.ucfirst($row->model).'Date']=$date->format(\DateTime::ATOM);
            }
        }
        else
        {
            $data=[
                'found'=>false
            ];
        }

        return response()->json([
            'data'=>$data
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }
}
