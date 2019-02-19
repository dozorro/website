<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SearchCustomerController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        if($request->has('edrpou')) {
            $query = "?edrpou=".$request->get('edrpou');
        } elseif($request->has('query')) {
            if(is_numeric($request->get('query'))) {
                $query = "?edrpou=".$request->get('query');
            } else {
                $query = "?query=".mb_strtolower(urlencode($request->get('query')));
            }
        }

        if(!isset($query) || empty(env('API_ORGSUGGEST'))) { return false; }

        $ch=curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, env('API_ORGSUGGEST').$query);

        if(env('API_LOGIN') && env('API_PASSWORD')){
            curl_setopt($ch, CURLOPT_USERPWD, env('API_LOGIN') . ":" . env('API_PASSWORD'));
        }

        $in=curl_exec($ch);

        curl_close($ch);

        $out=[];

        if($ch){
            $in=json_decode($in);

            if(!empty($in->items)){
                foreach($in->items as $one){
                    array_push($out, [
                        'key' => $one->edrpou,
                        'value'=> $one->short ? $one->short : $one->name
                    ]);
                }
            }
        }

        if($request->has('edrpou')) {
            return $out;
        } else {
            return new JsonResponse($out);
        }
    }
}
