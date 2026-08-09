<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Employee\Database\Factories\EmployeeResumeAttachmentFactory;
use Webkul\Security\Models\User;

class EmployeeResumeAttachment extends Model
{
    use HasFactory;

    /**
     * Upload constraints live on the model rather than in the Filament schema so
     * there is a single source of truth that can be asserted without booting the
     * form layer.
     */
    public const MAX_UPLOAD_SIZE = 10240;

    public const ACCEPTED_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'image/png',
        'image/jpeg',
        'image/webp',
    ];

    public const UPLOAD_DIRECTORY = 'employees/resumes';

    protected $table = 'employees_employee_resume_attachments';

    protected $fillable = [
        'employee_resume_id',
        'creator_id',
        'name',
        'file_path',
        'original_file_name',
        'mime_type',
        'file_size',
    ];

    protected $appends = ['url'];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(EmployeeResume::class, 'employee_resume_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    protected static function newFactory(): EmployeeResumeAttachmentFactory
    {
        return EmployeeResumeAttachmentFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attachment) {
            $attachment->creator_id ??= Auth::id();
        });

        static::saving(function ($attachment) {
            if (! $attachment->isDirty('file_path') || ! $attachment->file_path) {
                return;
            }

            $disk = Storage::disk('public');

            if (! $disk->exists($attachment->file_path)) {
                return;
            }

            $attachment->mime_type ??= $disk->mimeType($attachment->file_path);
            $attachment->file_size = $disk->size($attachment->file_path);
        });
    }
}
