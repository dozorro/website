<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App;
use App\ActualTender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BadgeController extends BaseController
{
    public function ngoCoins(Request $request, $slug) {

        $ngo = App\Models\NgoProfile::findBySlug($slug);
        $coins = $ngo->coins()->orderBy('dt', 'desc')->paginate(10);

        foreach($coins as &$coin) {
            $coin->_dt = $coin->dt->format('d.m.Y H:i');
            $coin->_type = $coin->type ? t('ngo.coin.is_auto') : t('ngo.coin.is_hand');
        }

        if($request->ajax()) {
            return response()->json($coins->toArray());
        }

        return $this->render('pages/ngo_coins', [
            'coins' => $coins,
            'ngo' => $ngo,
        ]);
    }

    public function coins(Request $request)
    {
        $query = App\Models\NgoProfile::select('id', 'slug', 'title')->byEnabled();
        $query->addSelect(DB::raw("(SELECT sum(coins.sum) as sum from perevorot_dozorro_coins as coins where coins.ngo_profile_id = perevorot_dozorro_ngo_profiles.id) as sumCoins"));
        $query->orderBy('sumCoins', 'DESC');
        $ngos = $query->paginate(10);

        if($request->ajax()) {
            if($ngos->isEmpty() || !$ngos[0]->sumCoins) {
                return response()->json(['data' => []]);
            } else {
                return response()->json($ngos->toArray());
            }
        }

        $page_model = new Page();
        $page = $page_model->where('url', '/ratings')->first();
        $locale = (trim($request->route()->getPrefix(), '/')) ?: App\Classes\Lang::getDefault();
        $page_blocks = false;

        if (!empty($page->{'longread_' . $locale})) {
            $blocks = (array)json_decode($page->{'longread_' . $locale});

            foreach ($blocks as $block) {
                if ($block->alias == 'badgesBlock') {
                    $badges = array_first($block->value->badges, function ($k, $v) {
                        return $v->slug == 'dozorrocoin';
                    });
                    $badges->badgesList = App\Models\Badge::whereIn('name', $badges->badge)->get();
                }
            }

            $page_blocks = new App\Classes\Longread($blocks, $page->id, $page->backendNamespace);
        }

        if ($blocks = $page_blocks ? $page_blocks->getBlocks() : null) {
            $blocks = array_shift($blocks);
            $blocks = $blocks->value;
        }

        return $this->render('pages/coins', [
            'ngos' => $ngos,
            'blocks' => $blocks,
            'badges' => $badges,
        ]);
    }

    public function badge(Request $request, $slug = null, $ngo = null)
    {
        if($slug == 'dozorrocoin') {
            if($ngo) {
                return $this->ngoCoins($request, $ngo);
            }
            return $this->coins($request);
        }

        if(in_array(env('APP_ENV'), ['local','dev','develop'])) {
            //Cache::forget('badges-page-'.$slug.'-' . App\Classes\Lang::getCurrentLocale());
        }

        $data = Cache::remember('badges-page-'.$slug.'-'.App\Classes\Lang::getCurrentLocale(), 60*24, function() use($request, $slug) {
            $page_model = new Page();
            $setting = App\Settings::instance('perevorot.dozorro.form');
            $page = $page_model->where('url', '/ratings')->first();
            $locale = (trim($request->route()->getPrefix(), '/')) ?: App\Classes\Lang::getDefault();
            $page_blocks = false;
            $badges = false;
            $ngos = null;

            if (!empty($page->{'longread_' . $locale})) {
                $blocks = (array)json_decode($page->{'longread_' . $locale});

                foreach ($blocks as $block) {
                    if ($block->alias == 'badgesBlock') {
                        $badges = array_first($block->value->badges, function ($k, $v) use ($slug) {
                            return $v->slug == $slug;
                        });

                        if($badges) {
                            $badges->badgesList = App\Models\Badge::whereIn('name', $badges->badge)->get();
                        }
                    }
                }

                $page_blocks = new App\Classes\Longread($blocks, $page->id, $page->backendNamespace);
            }

            if ($badges) {

                $bids = $badges->badgesList->lists('id')->toArray();

                if($badges->is_hand) {
                    $ngos = App\Models\Badge::getNgoProfiles2($bids);
                } else {
                    foreach ($setting as $k => $v) {
                        if (strpos($k, 'badge') && in_array((int)$v, $bids)) {
                            $schema = '';

                            if (stripos($k, 'F201') !== FALSE) {
                                $schema = 'F201';
                            } elseif (stripos($k, 'F202') !== FALSE) {
                                $schema = 'F202';
                            } elseif (stripos($k, 'F203') !== FALSE) {
                                $schema = 'F203';
                            } elseif (stripos($k, 'F204') !== FALSE) {
                                $schema = 'F204';
                            }

                            $nids = App\JsonForm::where('schema', $schema)->where('is_hide', 0)->groupBy('ngo_profile_id')->lists('ngo_profile_id')->toArray();
                            $ngos = App\Models\NgoProfile::whereIn('id', $nids)->orderByRaw("RAND(".time().")")->get();

                            foreach ($ngos as &$ngo) {
                                $form = $ngo->forms()->BySingleForm($schema)->where('is_hide', 0);

                                if ($schema == 'F204') {
                                    $form = $form->where('payload', 'like', '%"reason":"succes"%');
                                }

                                $ngo->forms = $form->count();

                                foreach ($setting as $_k => $_v) {
                                    if ($_k == $schema . "_price3" && ($ngo->forms >= $_v && $ngo->forms < $setting->{$schema . "_price2"})) {
                                        $ngo->_badge = array_first($badges->badgesList, function ($__k, $_badge) use ($setting, $schema) {
                                            return (int)$setting->{$schema . "_badge3"} == $_badge->id;
                                        });
                                    } elseif ($_k == $schema . "_price2" && ($ngo->forms >= $_v && $ngo->forms < $setting->{$schema . "_price1"})) {
                                        $ngo->_badge = array_first($badges->badgesList, function ($__k, $_badge) use ($setting, $schema) {
                                            return (int)$setting->{$schema . "_badge2"} == $_badge->id;
                                        });
                                    } elseif ($_k == $schema . "_price1" && (($ngo->forms >= $_v && $ngo->forms < $setting->{$schema . "_price2"}) || $ngo->forms >= $_v)) {
                                        $ngo->_badge = array_first($badges->badgesList, function ($__k, $_badge) use ($setting, $schema) {
                                            return (int)$setting->{$schema . "_badge1"} == $_badge->id;
                                        });
                                    }
                                }
                            }

                            break;
                        }
                    }

                    $array = $ngos;
                    $size = sizeof($array);

                    for ($i = 1; $i <= $size; $i++) {
                        for ($j = ($size - 1); $j >= $i; $j--) {
                            if ($array[$j - 1]->forms < $array[$j]->forms) {
                                $tmp = $array[$j - 1];
                                $array[$j - 1] = $array[$j];
                                $array[$j] = $tmp;
                            }
                        }
                    }

                    $ngos = $array;
                }
            }

            if ($blocks = $page_blocks ? $page_blocks->getBlocks() : null) {
                $blocks = array_shift($blocks);
                $blocks = $blocks->value;
            }

            return [
                'badges' => $badges,
                'ngos' => $ngos,
                'type' => $slug,
                'blocks' => $blocks,
            ];
        });

        return $this->render('pages/badge', $data);
    }
}
