<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\MvpMetrics;
use App\Models\MvpMetricsData;
use App\Models\MvpMetricsType;
use App\Models\MvpProfile;
use App\Models\MvpTemplate;
use App\Models\MvpTemplateLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Log;
use App;
use DB;
use Cache;
use Excel;

class MvpController extends BaseController
{
    public function saveTemplate(Request $request)
    {
        if(is_numeric($request->get('tpl'))) {
            if (!$tpl = MvpTemplate::find($request->get('tpl'))) {
                return false;
            } 

            $log = new MvpTemplateLog();
            $log->template_id = $request->get('tpl');
            $log->created_at = Carbon::now();
            $log->save();

            $response = new Response(['Set cookie...']);
            $role = $request->get('role');

            return $response
                ->withCookie(cookie('mvp_metrics_' . $role, $tpl->tpl, time() + (60 * 24 * 365), '/',
                    $request->getHost()))
                ->withCookie(cookie('mvp_metrics_selected_' . $role, $request->get('tpl'),
                    time() + (60 * 24 * 365), '/', $request->getHost()));
        } else {
            $response = new Response(['Set cookie...']);
            $role = $request->get('role');
            $tpl = $request->get('tpl');

            return $response
                ->withCookie(cookie('mvp_metrics_' . $role, $tpl, time() + (60 * 24 * 365), '/',
                    $request->getHost()))
                ->withCookie(cookie('mvp_metrics_selected_' . $role, ($tpl == 'all' ? 'all' : 'custom'),
                    time() + (60 * 24 * 365), '/', $request->getHost()));
        }
    }

    public function saveCustomTemplate(Request $request)
    {
        $role = $request->get('role');

        if($request->has('metrics')) {
            $tpl = $request->get('metrics');
            $metrics = [];

            foreach($tpl as $item) {
                $column = $item['column'];
                $row = $item['row'];
                $code = $item['code'];
                $metrics[($row . "-" . $column)] = $code;
            }

            $metrics = json_encode($metrics);
        } else {
            return false;
        }

        $log = new MvpTemplateLog();
        $log->json = $metrics;
        $log->created_at = Carbon::now();
        $log->save();

        if($this->user) {
            if($role == 'role1') {
                $this->user->user->custom_profile_PE = $metrics;
                $this->user->user->save();
            } else {
                $this->user->user->custom_profile_tenderer = $metrics;
                $this->user->user->save();
            }
        }

        $response = new Response(['data' => $metrics]);
        Artisan::call('cache:clear');

        return $response
            ->withCookie(cookie('mvp_metrics_custom_' . $role, $metrics, time() + (60 * 24 * 365), '/',
                $request->getHost()))
            ->withCookie(cookie('mvp_metrics_' . $role, $metrics, time() + (60 * 24 * 365), '/',
                $request->getHost()))
            ->withCookie(cookie('mvp_metrics_selected_' . $role, 'custom', time() + (60 * 24 * 365), '/',
                $request->getHost()))
            ->withCookie(cookie('mvp_metrics_tpl_id_' . $role, @$tpl->id, time() + (60 * 24 * 365), '/',
                $request->getHost()));
    }

    public function profileTable(Request $request, $scheme, $code, $setting_id, $export = false)
    {
        if(empty($this->profileAccess)) {
            if (!$this->user || !$this->user->is_profile) {
                abort(404);
            }
        }

        $cacheKey = md5($scheme.'-'.$code.'-'.$setting_id);
        $schemeOriginal = $scheme;
        $tmp = explode('-', $scheme);
        $id = array_pop($tmp);
        $scheme = implode('-', $tmp);

        if(env('APP_ENV') == 'local') {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, 60*24, function() use($request, $schemeOriginal, $scheme, $code, $setting_id, $id) {
            $profile = MvpProfile::find($id);

            if (empty($profile) || empty($setting_id)) {
                abort(404);
            }

            $settings = App\Models\MvpSetting::find($setting_id);
            $locale = App\Classes\Lang::getCurrentLocale();
            $blocks = (array)json_decode($settings->{'longread_' . $locale});
            $blocks = new App\Classes\Longread($blocks, $settings->id, $settings->backendNamespace);
            $blocks = $blocks->getBlocks();

            if (!empty($blocks)) {
                $blocks = array_filter($blocks, function ($v) {
                    return !empty($v->data) && $v->alias == 'table';
                });
            }

            $object = new \stdClass();
            $object->setting = $settings;

            $profileRiskTotal = 0;
            $profileRiskTotalDec = 0;

            if(Schema::hasTable('dozorro_risk_procuringEntity')) {
                $profileRiskTotal = DB::table('dozorro_risk_procuringEntity')->where('procuringEntity_identifier_id', $id)->value('risk_total');

                $profileRiskTotalHalf = !empty($profileRiskTotal) ? round($profileRiskTotal, 1) : 0;
                $profileRiskTotalDec = !empty($profileRiskTotal) ? round($profileRiskTotal, 1)*10 : 0;
                $profileRiskTotal = !empty($profileRiskTotal) ? round($profileRiskTotal, 2)*100 : 0;
            }

            $riskAccess = @App\Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->risk_in_search;

            return [
                'riskAccess' => $riskAccess,
                'profileRiskTotalHalf' => $profileRiskTotalHalf,
                'profileRiskTotalDec' => $profileRiskTotalDec,
                'profileRiskTotal' => $profileRiskTotal,
                'object' => $settings,
                'settings' => $settings,
                'blocks' => $blocks,
                'scheme' => $schemeOriginal,
                'profile' => $profile,
                'edrpou' => $id,
            ];
        });

        if($this->user && $export) {
            $this->export($data);
        } else {
            return $this->render('pages/profileTable', $data);
        }
    }

    public function export($data)
    {
        $fileName = $data['scheme'].'-'.current($data['blocks'])->value->title;
        $data2 = current($data['blocks'])->data['data'];
        $results = current($data['blocks'])->data['results'];
        $export = [];

        foreach($results as $row) {
            $ar = [];

            foreach($data2->table_fields as $item) {
                if (isset($row[$item->field_name])) {
                    $ar[$item->field_title] = $row[$item->field_name];
                }
            }

            $export[] = $ar;
        }
        
        Excel::create($fileName, function($excel) use($export, $fileName, $data) {
            $excel->sheet($data['scheme'], function($sheet) use($export) {
                $sheet->fromArray($export);
            });
        })->download('xls');
    }

    public function profile(Request $request, $scheme, $tplType = false, $roleType = false)
    {
        if(empty($this->profileAccess)) {
            if (!$this->user || !$this->user->is_profile) {
                abort(404);
            }
        }

        if(is_numeric($tplType) && $tplType == 0) {
            return redirect()->route('page.profile_by_id', ['scheme' => $scheme]);
        }

        $addKey = '';

        if($request->has('table')) {
            $addKey = '-'.$request->get('table');

            if($request->has('q')) {
                $addKey .= '-'.$request->get('q');
            }
        }

        $cacheKey = md5($scheme.'-'.$tplType.'-'.$roleType.$addKey);
        $schemeOriginal = $scheme;
        $tmp = explode('-', $scheme);
        $id = array_pop($tmp);
        $scheme = implode('-', $tmp);

        if(env('APP_ENV') == 'local') {
           Cache::forget($cacheKey);
        }
        
        $data = Cache::remember($cacheKey, 60*24, function() use($request, $scheme, $schemeOriginal, $id, $tplType, $roleType) {
            $tenders = App\JsonForm::where('entity_id', $id)->whereNotIn('schema', App\JsonForm::$ngoForms)->count();
            $profile = MvpProfile::find($id);

            if (empty($profile)) {
                abort(404);
            }

            $request = app(Request::class);
            $mtypes = $metrics = MvpMetricsType::where('type', 'metric')->get();
            $allMetricsData = MvpMetricsData::where('id', $id)->get();

            if(env('APP_ENV') == 'local') {
                if (isset($_GET['dump'])) {
                    dd($allMetricsData);
                }
            }

            foreach($metrics as $metric) {
                $metric->loadMvpData($allMetricsData);
            }

            $templates = [];
            $role1Href = '';
            $role2Href = '';

            if (in_array($profile->partyRole,
                    ['procuringEntity', 'both']) && (empty($roleType) || $roleType == 'role1')
            ) {
                $tpls = MvpTemplate::orderBy('is_default', 'DESC')->where('role', 1)->get();

                if (!$tpls->isEmpty()) {
                    $templates['role1']['templates'] = $tpls;
                }

                $roleType = !empty($roleType) ? $roleType : 'role1';
            } else {
                $roleType = !empty($roleType) ? $roleType : 'role2';
            }

            if (in_array($profile->partyRole, ['tenderer', 'both']) && $roleType == 'role2') {
                $tpls = MvpTemplate::orderBy('is_default', 'DESC')->where('role', 2)->get();

                if (!$tpls->isEmpty()) {
                    $templates['role2']['templates'] = $tpls;
                }
            }

            if (!empty($templates)) {
                $templates = (object)$templates;

                $tpl = $tplType;

                foreach ($templates as $role => &$_templates) {

                    $customTpl = null;

                    if(!$this->user && !is_numeric($tplType) && $tplType != 'all') {
                        return route('page.profile_by_id', [
                            'scheme' => $schemeOriginal,
                            'tpl' => $_templates['templates'][0]->id,
                            'role' => $role
                        ]);
                    }

                    if ($roleType && $roleType != $role) {
                        $tpl = false;
                    } else {
                        $tpl = $tplType;
                    }

                    foreach ($_templates['templates'] as $tk => $template) {


                        $setting = null;
                        $groupMetricsData = [];
                        $metricsData = null;
                        $selectedTpl = $request->cookie('mvp_metrics_selected_' . $role);

                        if (is_numeric($tpl) && $tpl != $template->id) {
                            continue;
                        }
                        if (is_numeric($selectedTpl) && is_numeric($selectedTpl) != $template->id) {
                            continue;
                        }

                        if (intval($tpl) == $template->id) {
                            $metricsData = json_decode($template->tpl, true);
                            $selectedTpl = $template->id;
                        } elseif (!empty($tpl) && !is_numeric($tpl) && $tpl != 'all') {
                            $metricsData = json_decode($tpl, true);
                            $selectedTpl = 'custom';

                            if (json_last_error()) {
                                $metricsData = null;
                                Log::error('MvpTpl ' . $tpl . ' error ' . json_last_error_msg());
                            }
                        } elseif ($request->cookie('mvp_metrics_selected_' . $role)) {
                            $metricsData = json_decode($request->cookie('mvp_metrics_' . $role), true);
                        } elseif (!$tk) {
                            $metricsData = json_decode($template->tpl, true);
                        }

                        if($this->user) {
                            if ($role == 'role1' && !empty($this->user->user->custom_profile_PE)) {
                                if($selectedTpl == 'custom') {
                                    $metricsData = json_decode($this->user->user->custom_profile_PE);
                                }

                                $customTpl = $this->user->user->custom_profile_PE;
                            } elseif ($role == 'role2' && !empty($this->user->user->custom_profile_tenderer)) {
                                if($selectedTpl == 'custom') {
                                    $metricsData = json_decode($this->user->user->custom_profile_tenderer);
                                }

                                $customTpl = $this->user->user->custom_profile_tenderer;
                            }
                        }

                        if (!empty($metricsData)) {
                            $this->groupTemplate($metricsData, $metrics, $groupMetricsData);
                            ksort($groupMetricsData);
                        }

                        if (is_numeric($selectedTpl) == $template->id) {
                            $setting = $template->setting;
                        } elseif (
                            !empty($request->cookie('mvp_metrics_tpl_id_' . $role)) &&
                            $request->cookie('mvp_metrics_tpl_id_' . $role) == $template->id
                        ) {
                            $setting = $template->setting;
                        } elseif (!$tk) {
                            $setting = $template->setting;
                            $selectedTpl = empty($selectedTpl) && empty($tpl) ? $template->id : $selectedTpl;
                            $selectedTpl = !empty($tpl) && !is_numeric($tpl) && $tpl !== 'all' ? 'custom' : $selectedTpl;
                        }

                        if ($setting) {
                            $locale = App\Classes\Lang::getCurrentLocale();
                            $blocks = (array)json_decode($setting->{'longread_' . $locale});
                            $blocks = new App\Classes\Longread($blocks, $setting->id, $setting->backendNamespace);
                            $blocks = $blocks->getBlocks();

                            if (!empty($blocks)) {
                                $blocks = array_filter($blocks, function ($v) {
                                    return !empty($v->value);
                                });
                            }
                        } else {
                            $blocks = null;
                            $tableBlocks = null;
                        }

                        $is_ajax = $request->method() == 'POST';

                        if ($is_ajax && $request->has('table')) {

                            $block = array_first($blocks, function ($key, $block) use ($request) {
                                return $block->alias == 'table' && $request->get('table') == $block->value->code;
                            });

                            if (!empty($block->data['error'])) {
                                return ['table' => null, 'error' => $block->data['error']];
                            }

                            return ['table' => !empty($block) ? $this->getTable($block) : null];
                        }

                        if (!empty($setting)) {
                            foreach ($metrics as $k => &$v) {
                                if (empty($v->mvp_data)) {
                                    unset($metrics[$k]);
                                } else {
                                    try {
                                        if (!empty($v->second_metric) && !is_object($v->second_metric)) {
                                            $v->second_metric = array_first($mtypes, function ($key, $item) use ($v) {
                                                return $v->second_metric == $item->code;
                                            });
                                        }
                                        if (!empty($v->third_metric) && !is_object($v->third_metric)) {
                                            $v->third_metric = array_first($mtypes, function ($key, $item) use ($v) {
                                                return $v->third_metric == $item->code;
                                            });
                                        }
                                    } catch(\Exception $e) {
                                        if(env('APP_DEBUG')) {
                                            //dd($v, $mtypes);
                                        }
                                    }
                                }
                            }

                            $_templates['blocks'] = $blocks;
                            $_templates['setting'] = $setting;
                            $_templates['groupMetricsData'] = $groupMetricsData;
                            $_templates['metrics'] = $metrics;
                            $_templates['selectedTpl'] = $selectedTpl;

                            if ($selectedTpl == 'custom') {
                                $_templates['href'] = \GuzzleHttp\json_encode($metricsData) . '/' . $role;
                            } elseif ($selectedTpl && $selectedTpl !== 'custom') {
                                $_templates['href'] = $selectedTpl . '/' . $role;
                            } elseif (!empty($tpl)) {
                                $_templates['href'] = $tpl . '/' . $role;
                            } elseif (!empty($template) && !$tk) {
                                $_templates['href'] = $template->id . '/' . $role;
                            }

                            if ($role == 'role1') {
                                $role1Href = $_templates['href'];
                            } elseif ($role == 'role2') {
                                $role2Href = $_templates['href'];
                            }
                        }
                    }

                    $_templates = (object)$_templates;
                }
            } else {
                $templates = [];
            }

            if (!empty($templates)) {
                if (empty($role1Href)) {
                    $role1Href = $request->cookie('mvp_metrics_selected_role1') == 'custom' ? $request->cookie('mvp_metrics_role1') : $request->cookie('mvp_metrics_selected_role1');

                    if (empty($role1Href)) {
                        $roleTpl = MvpTemplate::orderBy('is_default', 'DESC')->where('role', 1)->first();

                        if (empty($roleTpl)) {
                            $role1Href = '';
                        } else {
                            $role1Href = $roleTpl->id . '/role1';
                        }
                    } else {
                        $role1Href .= '/role1';
                    }
                }
                if (empty($role2Href)) {
                    $role2Href = $request->cookie('mvp_metrics_selected_role2') == 'custom' ? $request->cookie('mvp_metrics_role2') : $request->cookie('mvp_metrics_selected_role2');

                    if (empty($role2Href)) {
                        $roleTpl = MvpTemplate::orderBy('is_default', 'DESC')->where('role', 2)->first();

                        if (empty($roleTpl)) {
                            $role2Href = '';
                        } else {
                            $role2Href = $roleTpl->id . '/role2';
                        }
                    } else {
                        $role2Href .= '/role2';
                    }
                }
            }

            if (isset($_GET['dump'])) {
                dd($templates, $profile, $roleType, $tplType, $role1Href, $role2Href);
            }

            if (!empty($templates->role1)) {
                foreach ($templates->role1->groupMetricsData as $row => $array) {
                    foreach (range(1, 5) as $index) {
                        if (!isset($array[$index])) {
                            $templates->role1->groupMetricsData[$row][$index] = null;
                        }
                    }
                }

                foreach ($templates->role1->groupMetricsData as $row => &$array) {
                    $array = array_sort($array, function ($item, $key) {
                        return empty($item);
                    });
                }

                foreach ($templates->role1->metrics as $k => $type) {
                    if (stripos($type->code, 'P') === false) {
                        unset($templates->role1->metrics[$k]);
                    }
                }
            }
            if (!empty($templates->role2)) {
                foreach ($templates->role2->groupMetricsData as $row => $array) {
                    foreach (range(1, 5) as $index) {
                        if (!isset($array[$index])) {
                            $templates->role2->groupMetricsData[$row][$index] = null;
                        }
                    }
                }

                foreach ($templates->role2->groupMetricsData as $row => &$array) {
                    $array = array_sort($array, function ($item, $key) {
                        return empty($item);
                    });
                }

                foreach ($templates->role2->metrics as $k => $type) {
                    if (stripos($type->code, 'T') === false) {
                        unset($templates->role2->metrics[$k]);
                    }
                }
            }

            $groups = [];

            if ($tplType == 'all') {

                if ($roleType == 'role2') {
                    $role = 'T';
                } else {
                    $role = 'P';
                }

                $metrics = DB::select("select `group`, label, md.metric_value, mt.suffix, mt.display_decimals
                            from dozorro_profile_tenderer_metrics_types mt
                            left join dozorro_profile_tenderer_metrics_data md on (mt.code = md.metric_id)
                            where code like '{$role}%' and md.id = '{$id}'
                            order by `group`, `code`");

                $_groups = array_unique(array_column($metrics, 'group'));
                $groups = [];

                foreach ($_groups as $group) {
                    $groups[$group] = array_where($metrics, function ($key, $item) use ($group) {
                        return $group == $item->group;
                    });
                }
            }

            if(!empty($profile->tendersAsProcuringEntity) && !empty($profile->tendersAsTenderer)) {
                $aaa = $profile->tendersAsProcuringEntity >= $profile->tendersAsTenderer ? $profile->tendersAsProcuringEntity : $profile->tendersAsTenderer;
            } else {
                $aaa = !empty($profile->tendersAsProcuringEntity) ? $profile->tendersAsProcuringEntity : $profile->tendersAsTenderer;
            }

            $aaa = t('seo.profile.prozorro_tenders').' '.round(intval($aaa));

            $Р100 = array_first($allMetricsData, function($key, $item) {
                return $item->metric_id == 'P100';
            });
            $Т140 = array_first($allMetricsData, function($key, $item) {
                return $item->metric_id == 'T140';
            });

            $Р100 = !empty($Р100) ? $Р100->getOriginal('metric_value') : 0;
            $Т140 = !empty($Т140) ? $Т140->getOriginal('metric_value') : 0;
            $bbb = t('profile.seo.dogovoriv').' '.round(($Р100 >= $Т140 ? $Р100 : $Т140));

            if($roleType == 'role2') {
                $Т180 = array_first($allMetricsData, function($key, $item) {
                    return $item->metric_id == 'T180';
                });
                $Т180 = !empty($Т180) ? $Т180->getOriginal('metric_value') : 0;
                $vvv = t('profile.seo.peremog').' '.round($Т180).' - ';
            } else {
                $vvv = '';
            }

            $Р350 = array_first($allMetricsData, function($key, $item) {
                return $item->metric_id == 'P350';
            });
            $Т350 = array_first($allMetricsData, function($key, $item) {
                return $item->metric_id == 'T350';
            });

            $Р350 = !empty($Р350) ? $Р350->getOriginal('metric_value') : 0;
            $Т350 = !empty($Т350) ? $Т350->getOriginal('metric_value') : 0;
            $ggg = t('profile.seo.rozirvanuh_dogovoriv').' '.round(($Р350 >= $Т350 ? $Р350 : $Т350));

            $seo = [
                'title' => "{$profile->name} - {$id} - ".t('seo.profile.title'),
                'og_title' => "{$profile->name} - {$id} - ".t('seo.profile.title'),
                'description' => "{$profile->address} – $aaa – $bbb – $vvv{$ggg} – {$profile->name}",
                'og_description' => "{$profile->address} – $aaa – $bbb – $vvv{$ggg} – {$profile->name}",
            ];

            if(empty($customTpl)) {
                $customTpl = $request->cookie('mvp_metrics_custom_' . $roleType);
            }

            $profileRiskTotal = 0;
            $profileRiskTotalDec = 0;

            if(Schema::hasTable('dozorro_risk_procuringEntity')) {
                $profileRiskTotal = DB::table('dozorro_risk_procuringEntity')->where('procuringEntity_identifier_id', $id)->value('risk_total');
                //$profileRiskTotal = 0.563123;

                $profileRiskTotalHalf = !empty($profileRiskTotal) ? round($profileRiskTotal, 1) : 0;
                $profileRiskTotalDec = !empty($profileRiskTotal) ? round($profileRiskTotal*10, 1) : 0;
                $profileRiskTotal = !empty($profileRiskTotal) ? round($profileRiskTotal*100, 2) : 0;
                //dd($profileRiskTotalHalf, $profileRiskTotal, $profileRiskTotalDec);
            }

            $riskAccess = @App\Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->risk_in_search;

            return [
                'riskAccess' => $riskAccess,
                'profileRiskTotalHalf' => $profileRiskTotalHalf,
                'profileRiskTotalDec' => $profileRiskTotalDec,
                'profileRiskTotal' => $profileRiskTotal,
                'seo' => $seo,
                'groups' => $groups,
                'scheme' => $schemeOriginal,
                'groupTemplates' => $templates,
                'profile' => $profile,
                'roleType' => $roleType,
                'tplType' => $tplType,
                'role1Href' => $role1Href,
                'role2Href' => $role2Href,
                'edrpou' => $id,
                'reviews' => $tenders,
                'customTpl' => empty($customTpl) && $selectedTpl == 'custom' ? $tplType : $customTpl
            ];
        });

        if($request->ajax()) {
            return response()->json($data);
        } elseif(is_string($data)) {
            return redirect()->to($data);
        }

        $this->setSeoData($data['seo']);
        unset($data['seo']);

        return $this->render('pages/profile', $data);
    }

    public function getTable($block)
    {
        return view('partials.longread._blocks._table', [
            'data' => $block->data['data'],
            'results' => $block->data['results'],
            'ajax' => true,
            'single' => false,
        ])->render();
    }

    protected function groupTemplate($metricsData, $metrics, &$groupMetricsData)
    {
        foreach ($metricsData as $key => $code) {
            $k = explode('-', $key);

            $code = array_first($metrics, function ($k, $v) use ($code) {
                return $code == $v->code;
            });

            $groupMetricsData[$k[0]][$k[1]] = $code;
        }
    }

    public function profileExample()
    {
        return $this->render('pages/profileExample', []);
    }
}
