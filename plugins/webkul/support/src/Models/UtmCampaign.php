<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\UtmCampaignFactory;

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($utmCampaign) {
            $utmCampaign->creator_id ??= Auth::id();
        });
    }

    protected static function newFactory()
    {
        return UtmCampaignFactory::new();
    }
}
