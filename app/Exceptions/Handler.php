<?php namespace App\Exceptions;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\ErrorController;
use Exception;
use Request;
use Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
		/*
        $url=Request::url();

        Log::error('['.$e->getCode().'] "'.$e->getMessage().'" on line '.@$e->getTrace()[0]['line'].' of file '.@$e->getTrace()[0]['file'].' — '.$url);

        $url=Request::url();

        Log::info(Request::method().' '.$url);

        if(!empty(Request::all())){
            Log::info('', Request::all());
        }

        $file=$e->getFile();
        $line=$e->getLine();

        $filename=storage_path('logs/'.str_replace(['http://', 'https://', 'www.', '.', '/'], ['', '', '', '-', '-'], $url).'-'.str_replace('.php', '', last(explode('/', $file))).'-'.$line.'.log');

        if(!file_exists($filename))
            file_put_contents($filename, $url."\n\n".$file.' ('.$line.")\n".$e->getMessage());

        Log::info($file.' ('.$line.')');
        Log::info($e->getMessage());
        */
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
        if ($e instanceof NotFoundHttpException) {
            return app(ErrorController::class)->notfound();
        }

        if($e instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {
            $statusCode = 500;
        } else {
            $statusCode = $e->getCode() ? $e->getCode() : 500;
        }

        if(view()->exists('errors.'.$statusCode)) {

            if(app()->environment() == 'production') {
                $message = '';
            } else {
                $message = $e->getMessage();
            }

            if(!env('APP_DEBUG')) {
                return app(ErrorController::class)->systemerror2($message);
            }
            //return response()->view('errors.'.$statusCode, ['massage' => $message], $statusCode);
        }

        return parent::render($request, $e);
	}
}
