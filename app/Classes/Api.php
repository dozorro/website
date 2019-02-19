<?php

namespace App\Classes;

use App;
use Carbon\Carbon;
use GuzzleHttp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use App\Models\FormLog;

class Api
{
    private $api_url;

    private $client;
    private $jar;
    private $log;

    var $debug = false;

    public function __construct()
    {
        $handler_stack = GuzzleHttp\HandlerStack::create();
        $handler_stack->push(GuzzleHttp\Middleware::retry(function($retry, $request, $response, $reason) {

            /*
            Log::info('guzzle request: ' . (is_object($response) ? $response->getStatusCode() : $response)."\n");
            Log::info('guzzle value: ' . (is_object($response) ? $response->getBody() : $response)."\n");
            Log::info('guzzle reason: ' . json_encode($reason)."\n");
            */

            $correct = is_object($response) &&
                        ($response->getStatusCode() == '200' || $response->getStatusCode() == '201') &&
                        (isset(json_decode($response->getBody())->data) || isset(json_decode($response->getBody())->created)) &&
                        !json_last_error();

            //Log::info('guzzle correct: ' . (string)$correnct."\n");

            if($correct || $this->log->process == 'www') {
                return false;
            }

            $this->log->attempts = $retry+1;

            /*
            Log::info("--//--\n");
            Log::info('guzzle retry: ' . $this->log->attempts."\n");
            Log::info("--//--\n");
            */

            if($delay = env('API_FORMS_DELAY_RETRY', 0)) {

                if($this->log->attempts > 1) {
                    $mDelay = env('API_FORMS_DELAY_MULTIPLY', 1);
                    $mDelay = $mDelay ? $mDelay : 1;
                    $delay = $this->log->attempts * $mDelay;
                }

                sleep($delay);
            }

            return $this->log->attempts < env('API_FORMS_RETRY', 0);

        }));
        $this->client = new GuzzleHttp\Client(['handler' => $handler_stack]);
        $this->jar = new GuzzleHttp\Cookie\CookieJar();
        $this->api_url = env('API_FORMS_URL');
    }

    public function okGoogle($url) {

        $method = 'get';

        sleep(60);

        try {

            $response = $this->client->request($method, $url, [
                'headers' => [
                    'Content-Type' => 'text/html'
                ],
            ]);

            $response = (string)$response->getBody();

            return $response;
        } catch (GuzzleHttp\Exception\ClientException $e) {

            $xRequestId = !empty($e->getResponse()->getHeader('x-request-id')[0]) ? $e->getResponse()->getHeader('x-request-id')[0] : false;

            return false;
            //throw new \Exception('Ошибка: (' . $xRequestId . ') ' . $e->getMessage());
            //return 'error: ' . $e->getMessage();
        }
    }

    public function getForms($param = '')
    {
        $url = $this->api_url.$param;
        $method = 'get';

        $return='';
        $return_json='';

        $this->log=new FormLog();

        $this->log->url = $url;
        $this->log->process = 'cron-sync-list';
        $this->log->data = '';
        $this->log->object_id = '';
        $this->log->tender_id = '';
        $this->log->resolved = null;

        try {
            $response = $this->client->request($method, $url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'connect_timeout' => 3.14,
                'timeout' => 3.14
            ]);

            $response_string = (string) $response->getBody();
            $json = json_decode($response_string);

            $this->log->http_code = $response->getStatusCode();

            $return_json=!empty($json->data) ? $json : null;
            $return=$response_string;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $this->log->http_code=408;
            $this->log->resolved = 0;

            $return='';
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response=$e->getResponse();
            $this->log->resolved = 0;

            if(!empty($response)){
                $this->log->http_code=$response->getStatusCode();
            }

            $return='';
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->log->http_code = $response->getStatusCode();
            $this->log->resolved = 0;

            $return = (string) $e->getResponse()->getBody();
            $return_json = json_decode($return);
        }

        $this->log->response = $return;
        $this->log->created_at = Carbon::now();

        $this->log->save();

        return $return_json;
    }

    public function getForm($id)
    {
        $url = $this->api_url.'/'.$id;
        $method = 'get';
        $return='';
        $return_json='';

        $this->log=new FormLog();

        $this->log->url = $url;
        $this->log->process = 'cron-sync-form';
        $this->log->data = '';
        $this->log->object_id = $id;
        $this->log->tender_id = '';
        $this->log->resolved = null;

        try {
            $response = $this->client->request($method, $url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'connect_timeout' => 3.14,
                'timeout' => 3.14
            ]);

            $response_string = (string) $response->getBody();
            $json = json_decode($response_string);

            $this->log->http_code = $response->getStatusCode();

            $return_json=isset($json->data[0]) ? $json->data[0] : null;
            $return=$response_string;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $this->log->http_code=408;
            $this->log->resolved = 0;

            $return='';
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response=$e->getResponse();
            $this->log->resolved = 0;

            if(!empty($response)){
                $this->log->http_code=$response->getStatusCode();
            }

            $return='';
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->log->http_code = $response->getStatusCode();
            $this->log->resolved = 0;

            $return = (string) $e->getResponse()->getBody();
            $return_json = json_decode($return);
        }

        $this->log->response = $return;
        $this->log->created_at = Carbon::now();

        if(App\Models\BadTenderLog::where('object_id', $this->log->object_id)->first() && $this->log->http_code == '200') {
        } else {
            $this->log->save();
        }

        return $return_json;
    }

    public function sendForm(App\JsonForm $form, $process='www')
    {
        $return='';
        $url = $this->api_url;
        $method = 'PUT';

        $data = new \stdClass();
        $data->id = $form->object_id;
        $data->envelope = $form->getPayload();
        $signature = \Sodium\crypto_sign_detached($form->payload, file_get_contents(storage_path('api/keypair.dat')));
        $data->sign = rtrim(base64_encode($signature), "=");

        $url .= '/'.$data->id;

        $this->log=new FormLog();

        $this->log->url = $url;
        $this->log->process = $process;
        $this->log->object_id = $form->object_id;
        $this->log->tender_id = $form->tender;
        $this->log->data = json_encode($data);
        $this->log->resolved = null;

        try {
            $response = $this->client->request($method, $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => $data->envelope->owner,
                ],
                'connect_timeout' => 3.14,
                'timeout' => 3.14,
                'body' => json_encode($data)
            ]);

            $this->log->http_code=$response->getStatusCode();

            $response = (string) $response->getBody();

            $return=$response;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $this->http_code=408;
            $this->log->resolved = 0;

            $return='';
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response=$e->getResponse();
            $this->log->resolved = 0;

            if(!empty($response)){
                $this->log->http_code=$response->getStatusCode();
            }

            $return='';
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response=$e->getResponse();
            $this->log->http_code=$response->getStatusCode();
            $this->log->resolved = 0;

            $return=(string) $e->getResponse()->getBody();
        }

        $this->log->response=$return;
        $this->log->created_at=Carbon::now();

        $this->log->save();

        $return = json_decode($return);

        if(empty($return) && $this->log->process != 'www') {
            $return = false;
        }
        elseif(!empty($return) && !empty($return->error) && $this->log->http_code != '400' && $this->log->process != 'www') {
            $return = false;
        }
        elseif(!empty($return) && !empty($return->error) && $this->log->http_code == '400' && $this->log->process != 'www') {
            $return = true;
        }

        return $return;
    }

    public $json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;

    private function getCookies()
    {
        $this->client->request('GET', $this->api_url, [
            'cookies' => $this->jar,
            'http_errors' => false
        ]);
    }

    public static function cleanAccToken($str)
    {
        return preg_replace('/acc_token\=\w{32}/', 'acc_token=...', $str);
    }
}
