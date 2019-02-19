<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class MvpProfile extends Model
{
    use ModelTranslation;

    protected $table = 'dozorro_profile_entityMaster';

    public $backendNamespace = 'Perevorot\Dozorro\Models\MvpProfile';

    public function showType()
    {
        if($this->partyRole != 'tenderer') {
            return t('profile.header.label-1.'.$this->kind);
        } else {
            return t('profile.header.label-1.director');
        }
    }
}
