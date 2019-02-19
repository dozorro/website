<?php

namespace App\Http\Controllers;

use App\Models\Pair;
use App\Models\PairAnswer;
use App\Models\PairReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PairsController extends BaseController
{
    public function reviewUpdate(Request $request)
    {
        if($request->has('decision')) {
            PairReview::where('pair_id', $request->get('id'))->update([
                'reviewed' => $request->get('decision'),
                'date_reviewed' => Carbon::now(),
            ]);
        }

        return redirect()->route('page.pairs.review');
    }

    public function review()
    {
        if(!$this->user) {
            return $this->render('pages/pairs', ['pairsTotal' => 'auth']);
        }
        elseif(!$this->user->is_pairs_review) {
            abort(404);
        }

        $answers = PairReview::with(['pair','user'])->whereNull('reviewed')->orderBy('id')->get()->groupBy('pair_id')->first();

        if(!empty($answers)) {
            $pairsData = [];
            $this->getRisks($answers[0]->pair, $pairsData);
            $answers[0]->risks = current($pairsData);
        }

        return $this->render('pages/pairs/review', [
            'answers' => $answers,
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id = null, $type = null)
    {
        if(!$this->user) {
            return $this->render('pages/pairs/index', ['pairsTotal' => 'auth']);
        }
        elseif(!$this->user->is_pairs && !$this->user->is_pairs_button) {
            abort(404);
        }

        if(!empty($id) && $other = $this->checkMessageExist()) {
            $users = DB::table('dozorro_model_answers')->whereNull('answer')->where('user_email', $this->user->email)->get();
            $pairsTotal = Pair::whereIn('id', array_column($users, 'pair_id'))->count();

            return $this->render('pages/pairs/index', [
                'other' => $other,
                'pairsTotal' => $pairsTotal,
                'showCancel' => empty($id) || (!empty($type) && $type == 'prev')
            ]);
        }

        $data = $this->getData($request, $id, $type);
        $data['showIndicators'] = $type == 'info';

        return $this->render('pages/pairs/index', $data);
    }

    public function onAjax(Request $request, $id = false)
    {
        if(is_numeric($id) && $other = $this->checkMessageExist()) {
            return $other;
        }

        $data = $this->getData($request);
        $views = '';

        if($data['pairsTotal']) {
            foreach ($data['pairs'] as $k => $pair) {
                $views .= view('partials/_pair_item', [
                    'pair' => $pair,
                    'k' => $k,
                    'user' => $this->user,
                    'showCancel' => true,
                    'rightTenderIsNull' => $data['rightTenderIsNull'],
                ])->render();
            }
        }

        return $views;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->has('answer') && is_numeric($id)) {
            switch ($request->get('answer')) {
                case 'yes':
                    $answer = 1;
                    break;
                case 'no':
                    $answer = -1;
                    break;
                case 'skip':
                    $answer = 0;
                    break;
            }

            $answerPair = PairAnswer::
                where('pair_id', $id)
                ->where('user_email', $this->user->email)
                ->first();

            if(is_null($answerPair->answer) || ($answer != 0 && !is_null($answerPair->answer))) {
                if (!is_null($answerPair->answer)) {
                    $answerPair->updated = empty($answerPair->updated) ? 1 : $answerPair->updated + 1;
                }

                $answerPair->comment = $request->has('comment') ? $request->get('comment') : null;
                $answerPair->time_shown = empty($answerPair->time_shown) ? $request->get('dt') : $answerPair->time_shown;
                $answerPair->answer = $answer;
                $answerPair->favorities = !empty($request->get('favorite'));
                $answerPair->time_answered = Carbon::now();
                $answerPair->save();

                $params = [
                    'user_email' => $this->user->email,
                    'pair_id' => $id,
                    'answer' => $answerPair->answer,
                    'favorities' => $answerPair->favorities,
                    'time_answered' => Carbon::now(),
                    'time_shown' => $answerPair->time_shown
                ];

                if(empty($answerPair->updated)) {
                    DB::table('dozorro_model_review')->insert($params);
                }
                else {
                    DB::table('dozorro_model_review')
                        ->where([
                            'user_email' => $this->user->email,
                            'pair_id' => $id,
                        ])
                        ->update($params);
                }
            }
        }

        if($request->ajax()) {
            return response()->json(['status' => 'ok', 'html' => $this->onAjax($request, $id)]);
        } else {
            return redirect()->route('page.pairs');
        }
    }

    public function getData($request = null, $id = null, $type = null)
    {
        if(empty($id)) {
            $user = DB::table('dozorro_model_answers')->whereNull('answer')->where('user_email',
                $this->user->email)->first();
        } else {
            $user = DB::table('dozorro_model_answers')->where('user_email',
                $this->user->email)->where('pair_id', $id)->first();
        }

        if(empty($user)) {
           return ['pairsTotal' => 0];
        }

        $users = DB::table('dozorro_model_answers')->whereNull('answer')->where('user_email',
            $this->user->email)->get();

        $pairsTotal = Pair::whereIn('id', array_column($users, 'pair_id'))->count();
        $pairs = Pair::where('id', $user->pair_id)->get();

        if(empty($id)) {
            DB::table('dozorro_model_answers')
                ->whereIn('pair_id', array_column($pairs->toArray(), 'id'))
                ->where('user_email', $this->user->email)
                ->update([
                    'time_shown' => Carbon::now()
                ]);
        }

            $FormController = app('App\Http\Controllers\FormController');
            $FormController->search_type = 'tender';
            $rightTenderIsNull = false;

            foreach ($pairs as &$pair) {
                $this->getRisks($pair, $pairsData);

                if(empty($pair->right_tender_id)){
                    $rightTenderIsNull = true;
                }

                if($type == 'info') {
                    $tids = [];

                    if (!empty($pair->left_tender_id)) {
                        $tids[] = 'id=' . $pair->left_tender_id;
                    }
                    if (!empty($pair->right_tender_id)) {
                        $tids[] = 'id=' . $pair->right_tender_id;
                    }

                    $json = $FormController->getSearchResults($tids, true);
                    $data = json_decode($json);

                    if (!empty($data->items)) {
                        $left = array_first($data->items, function ($k, $v) use ($pair) {
                            return $v->id == $pair->left_tender_id;
                        });
                        $right = array_first($data->items, function ($k, $v) use ($pair) {
                            return $v->id == $pair->right_tender_id;
                        });

                        $pair->__left_tender_id = $left->tenderID;
                        $pair->__right_tender_id = $right->tenderID;
                    }

                    $pairsData['pair'] = $pair;
                }
            }

        return [
          'rightTenderIsNull' => $rightTenderIsNull,
          'pairsTotal' => $pairsTotal,
          'pairs' => $pairsData,
          'showCancel' => empty($id) || (!empty($type) && $type == 'prev'),
        ];
    }

    public function getRisks($pair, &$pairsData)
    {
        $values = DB::table('dozorro_risk_values')
            ->whereIn('tender_id', [$pair->left_tender_id, $pair->right_tender_id])
            ->orderBy('risk_code')
            ->get();

        if(empty($values)) {
            return;
        }

        $titles = DB::table('dozorro_risks')->whereIn('risk_code', array_column($values, 'risk_code'))->orderBy('risk_code')->get();

        $tenderLeft = array_where($values, function ($key, $item) use ($pair) {
            return $item->tender_id == $pair->left_tender_id && empty($item->lot_id);
        });
        $tenderRight = array_where($values , function($key, $item) use($pair) {
            return  $item->tender_id == $pair->right_tender_id && empty($item->lot_id);
        });

        if(!empty($pair->left_tender_lot_id)) {
            $lotLeft = array_where($values, function ($key, $item) use ($pair) {
                return $item->tender_id == $pair->left_tender_id && $item->lot_id == $pair->left_tender_lot_id;
            });

            if(!empty($lotLeft)) {
                $tenderLeft = array_merge($tenderLeft, $lotLeft);
            }
        }
        if(!empty($pair->right_tender_lot_id)) {
            $lotRight = array_where($values, function ($key, $item) use ($pair) {
                return $item->tender_id == $pair->right_tender_id && $item->lot_id == $pair->right_tender_lot_id;
            });

            if(!empty($lotRight)) {
                $tenderRight = array_merge($tenderRight, $lotRight);
            }
        }

        foreach($tenderLeft as $tender) {
            $title = array_first($titles, function($key, $title) use($tender) {
                return $tender->risk_code == $title->risk_code;
            });

            $pairsData[$pair->id][$tender->risk_code]['risk_title'] = !empty($title) ? $title->risk_title : '-';
            $pairsData[$pair->id][$tender->risk_code]['value_1'] = $tender->risk_value;

            if(!isset($pairsData[$pair->id][$tender->risk_code]['value_2'])) {
                $pairsData[$pair->id][$tender->risk_code]['value_2'] = '-';
            }
        }
        foreach($tenderRight as $tender) {
            $title = array_first($titles, function($key, $title) use($tender) {
                return $tender->risk_code == $title->risk_code;
            });

            $pairsData[$pair->id][$tender->risk_code]['risk_title'] = !empty($title) ? $title->risk_title : '-';
            $pairsData[$pair->id][$tender->risk_code]['value_2'] = $tender->risk_value;

            if(!isset($pairsData[$pair->id][$tender->risk_code]['value_1'])) {
                $pairsData[$pair->id][$tender->risk_code]['value_1'] = '-';
            }
        }

        ksort($pairsData[$pair->id]);
    }

    public function checkMessageExist()
    {
        $answers = DB::table('dozorro_model_answers')
            ->whereNotNull('answer')
            ->where('user_email', $this->user->email)
            ->count();

        if($answers) {
            $other = DB::table('dozorro_pairs_users_boost')
                ->where('user_email', $this->user->email)
                ->where('swipe_number', $answers)
                ->first();

            if(empty($other)) {
                $other = DB::table('dozorro_pairs_users_boost')
                    ->where('user_email', 'all')
                    ->where('swipe_number', $answers)
                    ->first();
            }

            if(!empty($other)) {

                $views = view('partials/_pair_item_other', [
                    'text' => $other->message,
                    'k' => t('pairs.message_step'),
                    'user' => $this->user,
                ])->render();

                return $views;
            }
        }

        return;
    }
}
