<?php

namespace Webkul\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class ApplicantCategory extends Model
{
    protected $table = 'recruitments_applicant_categories';

    protected $fillable = ['name', 'color', 'creator_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($applicantCategory) {
            $applicantCategory->creator_id ??= filament()->auth()->id();
        });
    }
}
