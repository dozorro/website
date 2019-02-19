<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class FeedbackRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules=[
            'email' => 'required|email',
            'phone' => 'required|regex:(\+\d{2} \(\d{3}\) \d{3}\-\d{2}\-\d{2})',
            'name' => 'required|regex:/^[(\w\s)]+$/u',
            'subject' => 'required',
            'message' => 'required',
            'type' => 'required',
            'g-recaptcha-response' => 'required|recaptcha',
        ];
        
        if(env('APP_DEBUG'))
            unset($rules['g-recaptcha-response']);

        return $rules;
    }
}