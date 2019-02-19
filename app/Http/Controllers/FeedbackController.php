<?php namespace App\Http\Controllers;

use App\Classes\JiraApi;
use App\Classes\User;
use App\Http\Requests;
use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function save(Requests\NewFeedbackRequest $request)
    {
        $error = '';

        if(User::isAuth() && !empty($request->all())) {
            $feedback = new Feedback();
            $feedback->title = $request->get('subject');
            $feedback->type = $request->get('type');
            $feedback->description = $request->get('text')."\n\n".$request->get('page');
            $feedback->email = User::isAuth()->email;
            $feedback->timestamp = Carbon::now();
            $feedback->save();

            $feedbackSetting = @Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->feedback;
            $jiraFields = @Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->jira_fields;

            if(!empty($feedbackSetting)) {

                $api = new JiraApi();

                /*
                $screens = $api->getScreens()->getResult();
                foreach($screens as $screen) {
                    $fields = $api->getScreenFields($screen['id'])->getResult();
                    print_r($fields);
                }
                print_r($screens);exit;
                */

                if(!empty($api->emailFieldId)) {
                    $fields['description'] = $request->get('text')."\n\n".$request->get('page');
                    $fields['customfield_' . $api->emailFieldId] = User::isAuth()->email;
                } else {
                    $fields['description'] = $request->get('text')."\n\n".$request->get('page')."\n\n".User::isAuth()->email;
                }

                $page = $request->get('page');
                $issueType = null;

                $type = array_first($feedbackSetting, function($key, $item) use($feedback) {
                    return $item['feedback_type'] == $feedback->type;
                });
                $jField = array_first($jiraFields, function($key, $item) use($page) {
                    $page = strtr($page, ['/#_=_'=>'','/#'=>'']);
                    return rtrim($page, '/') == rtrim($item['url'], '/');
                });
                if(empty($jField)) {
                    $jField = array_first($jiraFields, function ($key, $item) use ($page) {
                        return trim($item['field']) !== 'home' && stripos($page, $item['url']) !== false;
                    });
                }

                if(!empty($type)) {
                    $issueType = trim($type['jira_type']);
                    $types = $api->getIssueTypesJira();
                    $_t = array_first($types, function ($key, $item) use ($issueType) {
                        return $item->getName() == $issueType;
                    });

                    if(!empty($_t)) {
                        $issueType = $_t->getId();
                    }
                }
                if(!empty($jField)) {
                    $jFields = $api->getFieldsJira();

                    foreach($jFields as $kf => $fv) {
                        if(stripos($kf, 'customfield') !== false) {
                            if(trim($fv['name']) == 'Object') {
                                $fields[$fv['id']] = $jField['field'];
                            }
                        }
                    }
                }
                $error = $api->sendFeedbackToJira($feedback->title, $issueType, $fields);
            }
        }

        return response()->json(['status'=>'ok', 'issueType'=>$issueType, 'fields'=>$fields, 'error' => $error]);
    }

    public function store(FeedbackRequest $request)
    {
                return redirect()->back()->withErrors([
                    'done'=>true
                ]);
                        $data=$request->input();

        $url=env('WORKSECTION_URL').'/api/admin/';
        $action='post_task';
        $page='/project/'.env('WORKSECTION_PROJECT_ID').'/';
        
        $data=[
            'email_user_from'=>'andriy.kucherenko@gmail.com',
            'email_user_to'=>'andriy.kucherenko@gmail.com',
            'title'=>trans('feedback.type.'.$data['type']).(!empty($data['id'])?' '.$data['id']:''),
            'text'=>sprintf('%s<br>%s<br><br><strong>URL</strong>: <a href="%s">%s</a><br><strong>Контактна особа</strong>: %s <%s> %s',
                $data['subject'],
                $data['message'],
                $request->server('HTTP_REFERER'),
                $request->server('HTTP_REFERER'),
                $data['name'],
                $data['email'],
                $data['phone']
            ),
            'action'=>$action,
            'page'=>$page,
            #'hidden'=>'email,email',
            #'subscribe'=>'email,email',
            #'priority'=>'',
            'datestart'=>date('d.m.Y'),
            #'dateend'=>'DD.MM.YYYYY',
            'hash'=>md5($page.$action.env('WORKSECTION_KEY')),
        ];

        $query=$url.'?'.http_build_query($data);
        $header=get_headers($url)[0];

        if(strpos($header, '200 OK')!==false)
        {
            $ch=curl_init();
    
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $query);
    
            $result=curl_exec($ch);
            curl_close($ch);

            if($result && $json=json_decode($result))
            {
                if(!empty($json->status) && $json->status=='error')
                {
                    return redirect()->back()->withErrors([
                        'api'=>'Помилка підключення до API'
                    ]);
                }

                return redirect()->back()->withErrors([
                    'done'=>true
                ]);
            }
        }

        return redirect()->back()->withErrors([
            'api'=>'Помилка підключення до API'
        ]);
    }
}
