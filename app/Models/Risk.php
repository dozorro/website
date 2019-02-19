<?php

namespace App\Models;

use App\File;
use App\Helpers;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    protected $table = 'dozorro_risks';
    protected $primaryKey = 'risk_code';
    public $incrementing = false;

    public $backendNamespace = 'Perevorot\Dozorro\Models\Risk';

    public function get_image() {
        return File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->risk_code)
            ->where('field', 'image')
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getImageAttribute()
    {
        $file = File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->risk_code)
            ->where('field', 'image')
            ->orderBy('id', 'DESC')
            ->first();

        if($file)
        {
            return $file = Helpers::getStoragePath($file->disk_name);
        }
        else
        {
            return '';
        }
    }
}
