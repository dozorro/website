<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class NewFeedbackRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules=[
            'subject' => 'required',
            'text' => 'required',
            'type' => 'required',
        ];

        return $rules;
    }
}