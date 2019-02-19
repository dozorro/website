<?php

namespace App\Classes\Blocks;

use App\Models\User;
use App\Models\NgoProfile;
use Carbon\Carbon;
use App\Customer;
use App\Helpers;
use DB;
use App\JsonForm;
use Psy\Util\Json;
use App\Classes\Lang;
use Illuminate\Support\Facades\Cache;

/**
 * Class CustomersBlock
 * @package App\Classes\Blocks
 */
class CustomersBlock extends IBlock
{
    /**
     * @return array
     */
    public function get()
    {
        return Cache::remember('customer-block-'.Lang::getCurrentLocale(), 60*24, function(){
            $users = NgoProfile::where('is_enabled', 1)->where('is_index', $this->block->value->ngos_is_index)->get()->each(function ($item, $key) {
                return $item ? $item->translate() : null;
            });

            $ngo = [];

            if(!$users->isEmpty()) {
                foreach($users AS $k => $customer) {
                    $ngo[$customer->id]['customer'] = $customer;
                    $ngo[$customer->id]['customer_id'] = $customer->id;
                    $ngo[$customer->id]['forms'] = $customer->count_forms;
                }
            }

            if(count($ngo) > 1) {
                $data_year = array();

                foreach ($ngo as $key => $arr) {
                    $data_year[$key] = $arr['forms'];
                }

                array_multisort($data_year, SORT_DESC, $ngo);
            }

            if(count($ngo) > 6) {
                $ngo = array_slice($ngo, 0, 6);
            }

            if(!empty($ngo)) {
                $customers_ids = array_unique(array_pluck($ngo, 'customer_id'));

                $logotypes = DB::table('system_files')
                    ->where('field', 'image')
                    ->where('is_public', true)
                    ->where('attachment_type', 'Perevorot\Dozorro\Models\NgoProfile')
                    ->whereIn('attachment_id', $customers_ids)
                    ->get();

                foreach ($ngo AS $k => $_customer) {
                    $customer = $_customer['customer'];
                    $logotype = array_first($logotypes, function ($k, $logotype) use ($customer) {
                        return $logotype->attachment_id == $customer->id;
                    });

                    if (!empty($logotype)) {
                        $customer->logo = Helpers::getStoragePath($logotype->disk_name);
                    }

                    $ngo[$k]['customer'] = $customer;
                    $ngo[$k]['forms'] = $customer->count_forms;
                };

                $ngo = array_where($ngo, function ($key, $customer) {
                    return !empty($customer['customer']->logo);
                });
            }

            $customers = Customer::where('is_enabled', 1)->where('is_index', $this->block->value->customers_is_index)->get()->each(function ($item, $key) {
                return $item ? $item->translate() : null;
            });

            $_customers = [];

            foreach($customers AS $customer) {
                if($customer->edrpou) {

                    $edrpous = array_filter(array_map(function($ev) {
                        return trim($ev);
                    }, explode("\n", $customer->edrpou)), function($ev) {
                            return $ev;
                    });

                    $_customers[$customer->main_edrpou]['customer'] = $customer;
                    $_customers[$customer->main_edrpou]['customer_id'] = $customer->id;

                    $_customers[$customer->main_edrpou]['comments'] = $customer->count_comments;
                    $_customers[$customer->main_edrpou]['reviews'] = $customer->count_forms;
                }
            }

            foreach($_customers AS $_k => $customer) {
                $customer['avg_forms'] = 0;
                $customer['edrpou'] = $_k;

                if ($customer['comments'] && $customer['reviews']) {
                    $customer['avg_forms'] = count($customer['comments']) / count($customer['reviews']);
                }

                $_customers[$_k] = $customer;
            }

            if(count($_customers) > 1) {
                $data_year = array();

                foreach ($_customers as $key => $arr) {
                    $data_year[$key] = $arr['avg_forms'];
                }

                array_multisort($data_year, SORT_DESC, $_customers);
            }

            if(count($_customers) > 6) {
                $_customers = array_slice($_customers, 0, 6);
            }

            $customers_ids=array_unique(array_pluck($_customers, 'customer_id'));

            $logotypes=DB::table('system_files')
                ->where('field', 'image')
                ->where('is_public', true)
                ->where('attachment_type', 'Perevorot\Dozorro\Models\Customer')
                ->whereIn('attachment_id', $customers_ids)
                ->get();

            foreach($_customers AS $k => $_customer) {
                $customer = $_customer['customer'];

                $logotype=array_first($logotypes, function($k, $logotype) use ($customer){
                    return $logotype->attachment_id==$customer->id;
                });

                if(!empty($logotype)){
                    $customer->logo=Helpers::getStoragePath($logotype->disk_name);
                }

                if(!empty($customer->edrpou)){
                    $customer->url='/edrpou/?code='.str_replace("\r\n", ",", $customer->edrpou);
                }

                $_customers[$k]['customer'] = $customer;
            };

            $_customers=array_where($_customers, function($key, $customer){
                return !empty($customer['customer']->logo);
            });

            return (object) [
                'customers' => $_customers,
                'ngo' => $ngo,
            ];
        });
    }
}
