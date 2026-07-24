<?php

namespace Webkul\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class ApplicantCategory extends Model
{
    protected $table = 'recruitments_applicant_categories';

    protected $fillable = ['name', 'color', 'creator_id'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($applicantCategory) {
            $applicantCategory->creator_id ??= Auth::id();
        });
    }
}
