<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Models\Monitoring\Monitoring;
use App\Models\NgoProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CommunityController extends BaseController
{
    public $pagination = 5;

    public function index(Request $request, $activeTab = 'ngo') {

        $request->session()->put('seed', time());

        return $this->render('pages/community', ['activeTab' => $activeTab]);
    }

    public function monitoring(Request $request) {

        if(!$request->has('ajax')) {
            return $this->index($request, 'monitoring');
        }

        $monitoring = ($this->user && isset($this->user->monitoring) && $this->user->monitoring && $this->user->access_read) ?
            Monitoring::byEnabled()->orderByRaw("RAND(".$request->session()->get('seed').")")->paginate($this->pagination)
            : new Collection([]);

        foreach($monitoring AS $k => $item) {
            $monitoring[$k] = $item->translate();
        }

        return $this->render('pages/community/monitoring', [
            'monitoring' => $monitoring,
        ]);
    }

    public function customers(Request $request) {

        if(!$request->has('ajax')) {
            return $this->index($request, 'customers');
        }

        $edrpou = false;

        if(!empty($this->user)) {
            $edrpou = $this->user->main_edrpou;
        }

        $customers = Customer::
            where('is_enabled', 1)
            ->where(function($query) use($edrpou) {
                if($edrpou) {
                    $query->where('is_closed', 0)
                        ->orWhereNull('is_closed')
                        ->orWhere(function($query) use($edrpou) {
                            $query->where('is_closed', 1)
                                ->where('main_edrpou', $edrpou);
                        });
                } else {
                    $query->where('is_closed', 0)
                        ->orWhereNull('is_closed');
                }
            })
            ->orderByRaw("RAND(".$request->session()->get('seed').")")
            ->paginate($this->pagination);

        foreach($customers AS $k => $item) {
            $customers[$k] = $item->translate();
        }

        return $this->render('pages/community/customers', [
            'customers' => $customers,
        ]);
    }

    public function ngo(Request $request) {
        if(!$request->has('ajax')) {
            return $this->index($request, 'ngo');
        }

        $closed = !empty($this->user->ngo) && $this->user->ngo->is_closed;

        $users = NgoProfile::
            byEnabled()
            ->where(function($query) use($closed) {
                if(!$closed) {
                    $query->where('is_closed', 0)
                    ->orWhereNull('is_closed');
                }
            })
            ->orderByRaw("RAND(".$request->session()->get('seed').")")
            ->paginate($this->pagination);

        foreach($users AS &$ngo) {
            $ngo = $ngo->translate();
            //$ngo->badges = $ngo->getBadges();
            $ngo->count_authors_posts = $ngo->authors_posts();
            $ngo->logo = $ngo->show_logo();
            $ngo->additional = $ngo->getAdditionalData();
        }

        return $this->render('pages/community/ngo', [
            'ngos' => $users,
            'showFilter' => false
        ]);
    }

    public function ajaxNgoHeader(Request $request) {
        if($request->ajax() && $request->get('id')) {

            $ngo = NgoProfile::find($request->get('id'));

            $dates['date_to'] = $request->get('date_to');
            $dates['date_from'] = $request->get('date_from');

            $ngo->_date = $dates;

            $ngo = $ngo->translate();
            $ngo->badges = $ngo->getBadges();
            $ngo->count_authors_posts = $ngo->authors_posts();
            $ngo->logo = $ngo->show_logo();
            $ngo->additional = $ngo->getAdditionalData();

            $views = view('partials/_ngo_analytics', [
                'ngo' => $ngo,
            ])->render();

            return ['html' => $views];
        }
    }
}
