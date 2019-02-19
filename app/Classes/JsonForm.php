<?php

namespace App\Classes;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Input;
use App\Settings;

/**
 * Class JsonForm
 * @package App\Classes
 */
class JsonForm
{
    /**
     * @var string
     */
    private $form;
    private $formCode;

    private $jsonContent;
    private $formData;

    /**
     * JsonForm constructor.
     * @param $form
     */
    public function __construct($form, $formCode='', $formData=false)
    {
        $this->form = $form;
        $this->formCode = $formCode;

        if($formData)
        {
            $this->formData = [
                'form'=>$formData
            ];
        }
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    public function getFormFile()
    {
        if (!file_exists($this->form)) {
            $jsonFormPath = storage_path().'/forms/'.$this->formCode.'.json';

            if(!file_exists($jsonFormPath)){
                throw new FileNotFoundException();
            }

            $json=json_decode(file_get_contents($jsonFormPath));

            $data=$this->parseSettings(@Settings::instance('perevorot.dozorro.form')->{$this->formCode});
            $update_field=@Settings::instance('perevorot.dozorro.form')->{$this->formCode.'_field'};
    
            if(!empty($data) && !empty($update_field) && !empty($json->properties->formData->form))
            {
                foreach($json->properties->formData->form as $field)
                {
                    if($field->key==$update_field){
                        $field->options=$data;
                    }
                }
            }

            return json_encode($json);
        }

        return file_get_contents($this->form);
    }

    /**
     * @return array|mixed
     */
    private function handleFormContent()
    {
        $jsonContent = $this->getFormFile();

        $this->jsonContent = $jsonContent;

        $jsonForm = json_decode($jsonContent, true);
        $jsonFormArray = array_dot($jsonForm);
        $formContent = [];

        foreach($jsonFormArray as $k => $one)
        {
            if(strpos($k, '.form.') !== false)
            {
                $key = substr($k, 0, strpos($k, '.form.'));
                $formContent = array_get($jsonForm, $key);

                break;
            }
        };

        return $formContent;
    }

    /**
     * @return mixed
     */
    public function getJsonContent()
    {
        return $this->jsonContent;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
        $input=!empty($this->formData) ? $this->formData : Input::get();

        $this->handleFormContent();
        $form=json_decode($this->jsonContent, true);

        $required=false;
        
        if(empty($form['properties']['formData']['properties']))
            return false;

        if(!$required)
        {
            $required=!empty($form['properties']['formData']['required']) ? $form['properties']['formData']['required'] : array_keys(array_where($form['properties']['formData']['properties'], function($k, $property){
                return !empty($property['required']);
            }));
        }

        if(empty($required))
            return true;

        $update_field=@Settings::instance('perevorot.dozorro.form')->{$this->formCode.'_field'};
        $update_code_field=@Settings::instance('perevorot.dozorro.form')->{$this->formCode.'_code_field'};

        if(!empty($update_field) && !empty($update_code_field))
        {
            $data=$this->parseSettings(Settings::instance('perevorot.dozorro.form')->{$this->formCode}, true);
            
            $input['form'][$update_code_field]=array_search(array_get($input, 'form.'.$update_field), $data);
        }

        $is_valid = true;

        foreach($required as $field)
        {
            if(empty(array_get($input, 'form.'.$field)))
                $is_valid = false;
        }

        return $is_valid;
    }
    
    private function parseSettings($text, $return_array=false)
    {
        if(empty($text))
            return (object)[];

        $array=explode("\r\n", trim($text));
        $out=[];
        
        foreach($array as $k=>$one)
        {
            $ar=explode('=', trim($one));
            $out[$ar[0]]=$ar[1];
        }

        return $return_array ? $out : (object) $out;
    }
}
