<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class UtmCampaign extends Model
{
    use HasFactory;

    protected $table = 'utm_campaigns';

    protected $fillable = [
        'user_id',
        'stage_id',
        'color',
        'creator_id',
        'name',
        'title',
        'is_active',
        'is_auto_campaign',
        'company_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stage()
    {
        return $this->belongsTo(UtmStage::class, 'stage_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
