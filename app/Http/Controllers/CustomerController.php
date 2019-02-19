<?php

namespace App\Http\Controllers;

use App\Customer;
use App\JsonForm;
use App\Models\Monitoring\Monitoring;
use App\Models\NgoProfile;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CustomerController extends BaseController
{
    public $pagination = 5;
    public $activeTab = 'tenders';

    public function index($edrpou = null) {

        if(!$edrpou || !$customer = Customer::findByEdrpou($edrpou)) {
            abort(404);
        }
        elseif($customer->is_closed && (!$this->user || $this->user->main_edrpou != $customer->main_edrpou)) {
            abort(404);
        }

        $FormController = app('App\Http\Controllers\FormController');
        $dataStatus = [];

        foreach($FormController->get_status_data() as $one)
            $dataStatus[$one['id']] = $one['name'];

        foreach($FormController->get_region_data() as $one)
            $regions[$one['id']] = $one['name'];

        setlocale(LC_ALL, "en_US.UTF-8");
        asort($regions, SORT_LOCALE_STRING);

        return $this->render('pages/customers', [
            'activeTab' => $this->activeTab,
            'customer' => $customer,
            'dataStatus' => $dataStatus,
            'regions' => $regions,
            'edrpou' => $edrpou,
            'tenders' => [],
        ]);
    }

    public function tenders($edrpou, $type, Request $request) {

        if(!$request->has('ajax')) {
            $this->activeTab = $type;
            return $this->index($edrpou);
        }

        if(!$edrpou || !$customer = Customer::findByEdrpou($edrpou)) {
            return null;
        }

        $params = $request->all();
        $edrpou = [];

        if(!$customer->is_by_region) {
            if ($customer->edrpou) {
                $edrpou = explode("\n", str_replace("\r", '', trim($customer->edrpou)));
            }
            if ($customer->main_edrpou) {
                $edrpou[] = trim($customer->main_edrpou);
            }
        }

        $params['type'] = $type;
        $params['edrpou'] = $edrpou;
        $params['region'] = isset($params['region']) && $params['region'] ? $params['region'] : $customer->region;
        $tenders = JsonForm::getReviews($params);

        if($request->ajax() && $request->get('page') > 1) {

            $views = '';
            $views_mobile = '';
            $FormController = app('App\Http\Controllers\FormController');
            $dataStatus = [];

            foreach($FormController->get_status_data() as $one)
                $dataStatus[$one['id']] = $one['name'];

            foreach($tenders AS $item) {
                $views .= view('partials/_search_tender', [
                    'item' => $item->get_tender_json(),
                    'tender' => $item,
                    'dataStatus' => $dataStatus,
                    'for_mobile' => false,
                ])->render();

                $views_mobile .= view('partials/_search_tender', [
                    'item' => $item->get_tender_json(),
                    'tender' => $item,
                    'dataStatus' => $dataStatus,
                    'for_mobile' => true,
                ])->render();
            }

            return ['desktop' => $views, 'mobile' => $views_mobile];
        }

        return $this->render('pages/customers/tenders', [
            'tenders' => $tenders,
        ]);
    }
}