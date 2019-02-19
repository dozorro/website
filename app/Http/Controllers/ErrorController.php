<?php

namespace App\Http\Controllers;

class ErrorController extends BaseController
{
    public function notfound()
    {
        $this->setSeoData([
            'title' => 'page not found',
        ]);

        return $this->render('errors.404', [], 404);
    }

    public function systemerror2($message = '')
    {
        $this->setSeoData([
            'title' => 'system error',
        ]);

        return $this->render('errors.500', ['message' => $message], 500);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function systemerror()
    {
        $data = [
            'html' => app('App\Http\Controllers\PageController')->get_html(),
        ];

        return $this->render('errors/500', $data);
    }
}
