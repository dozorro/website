<?php

namespace App\Models;

use App\File;
use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;

class NgoDonor extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_dozorro_ngo_donors';
    public $backendNamespace = 'Perevorot\Dozorro\Models\NgoDonor';

    public function ngo_profile()
    {
        return $this->belongsTo('App\Models\NgoProfile', 'ngo_profile_id', 'id');
    }

    public function image()
    {
        $file = File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
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
