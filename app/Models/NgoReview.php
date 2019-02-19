<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class NgoReview extends Model
{
    use ModelTranslation;

    public $dates = ['created_at'];

    CONST REVIEW_STATUS1 = 1;
    CONST REVIEW_STATUS2 = 2;
    CONST REVIEW_STATUS3 = 3;

    public static $statusData = [
        self::REVIEW_STATUS1 => 'ngo.review.status_success',
        self::REVIEW_STATUS2 => 'ngo.review.status_not_success',
        self::REVIEW_STATUS3 => 'ngo.review.status_cancel',
    ];

    protected $table = 'perevorot_dozorro_ngo_reviews';
    public $backendNamespace = 'Perevorot\Dozorro\Models\NgoProfileReview';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function ngo_profile()
    {
        return $this->belongsTo('App\Models\NgoProfile', 'ngo_profile_id', 'id');
    }

    public function scopebyStatus($query, $data)
    {
        if($data == 'new') {
            return $query->whereNull('status');
        } elseif($data == 'complete') {
            return $query->whereNotNull('status');
        }
    }
}
